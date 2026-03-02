<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Services\AuditService;

final class ModuleController
{
    public function handle(string $path, string $method): void
    {
        $user = Auth::user();
        if (!$user) {
            redirect('/login');
        }

        $parts = explode('/', trim($path, '/'));
        $module = $parts[1] ?? 'inventory';
        $db = Database::connection();

        if ($method === 'POST') {
            verify_csrf();
            $this->handlePost($module, $user, $db);
            redirect('/module/' . $module);
        }

        $data = $this->loadModuleData($module, $user, $db);
        View::render('modules/module', [
            'module' => $module,
            'data' => $data,
            'user' => $user,
        ]);
    }

    private function companyId(array $user): int
    {
        if ($user['role'] === 'admin') {
            $companyId = (int)($_GET['company_id'] ?? $_POST['company_id'] ?? 0);
            return $companyId;
        }

        return (int)$user['company_id'];
    }

    private function assertRole(array $user, array $roles): void
    {
        if (!in_array($user['role'], $roles, true)) {
            http_response_code(403);
            exit('Access denied.');
        }
    }

    private function handlePost(string $module, array $user, \PDO $db): void
    {
        $action = (string)($_POST['action'] ?? '');
        $companyId = $this->companyId($user);

        if ($module === 'inventory') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'pharmacist']);

            if ($action === 'create_supplier') {
                $stmt = $db->prepare('INSERT INTO suppliers (company_id,name,contact_person,phone,email,lead_time_days,notes) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$companyId, $_POST['name'], $_POST['contact_person'], $_POST['phone'], $_POST['email'], (int)$_POST['lead_time_days'], $_POST['notes']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'supplier', (int)$db->lastInsertId());
            }

            if ($action === 'create_medicine') {
                $stmt = $db->prepare('INSERT INTO medicines (company_id,supplier_id,name,generic_name,therapeutic_class,barcode,hsn_code,purchase_price,selling_price,min_stock,allergy_tags,narcotic) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
                $stmt->execute([
                    $companyId,
                    (int)($_POST['supplier_id'] ?? 0) ?: null,
                    $_POST['name'],
                    $_POST['generic_name'],
                    $_POST['therapeutic_class'],
                    $_POST['barcode'],
                    $_POST['hsn_code'],
                    (float)$_POST['purchase_price'],
                    (float)$_POST['selling_price'],
                    (int)$_POST['min_stock'],
                    $_POST['allergy_tags'],
                    isset($_POST['narcotic']) ? 1 : 0,
                ]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'medicine', (int)$db->lastInsertId());
            }

            if ($action === 'create_batch') {
                $stmt = $db->prepare('INSERT INTO medicine_batches (medicine_id,batch_number,storage_zone,qty,expiry_date) VALUES (?,?,?,?,?)');
                $stmt->execute([(int)$_POST['medicine_id'], $_POST['batch_number'], $_POST['storage_zone'], (int)$_POST['qty'], $_POST['expiry_date']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'medicine_batch', (int)$db->lastInsertId());
            }

            if ($action === 'auto_reorder') {
                $sql = "SELECT m.id,m.supplier_id, COALESCE(SUM(b.qty),0) AS stock_qty, m.min_stock
                        FROM medicines m
                        LEFT JOIN medicine_batches b ON b.medicine_id=m.id
                        WHERE m.company_id=?
                        GROUP BY m.id,m.supplier_id,m.min_stock
                        HAVING stock_qty <= m.min_stock AND m.supplier_id IS NOT NULL";
                $stmt = $db->prepare($sql);
                $stmt->execute([$companyId]);
                $rows = $stmt->fetchAll();

                foreach ($rows as $row) {
                    $po = $db->prepare('INSERT INTO purchase_orders (company_id,supplier_id,status,auto_generated,notes) VALUES (?,?,?,?,?)');
                    $po->execute([$companyId, (int)$row['supplier_id'], 'draft', 1, 'Auto-generated at min-threshold']);
                    $poId = (int)$db->lastInsertId();

                    $qty = max(10, ((int)$row['min_stock'] * 2) - (int)$row['stock_qty']);
                    $poi = $db->prepare('INSERT INTO purchase_order_items (purchase_order_id,medicine_id,qty,expected_price) VALUES (?,?,?,?)');
                    $poi->execute([$poId, (int)$row['id'], $qty, 0]);
                    AuditService::log($companyId, (int)$user['id'], 'auto_reorder', 'purchase_order', $poId);
                }
            }

            return;
        }

        if ($module === 'pos') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'billing', 'pharmacist']);
            if ($action === 'create_sale') {
                $medicineId = (int)$_POST['medicine_id'];
                $qty = (int)$_POST['qty'];
                $payment = (string)$_POST['payment_mode'];
                $taxMode = (string)$_POST['tax_mode'];

                $med = $db->prepare('SELECT * FROM medicines WHERE id=? LIMIT 1');
                $med->execute([$medicineId]);
                $medicine = $med->fetch();
                if (!$medicine) {
                    return;
                }

                $batch = $db->prepare('SELECT * FROM medicine_batches WHERE medicine_id=? AND qty>0 ORDER BY expiry_date ASC LIMIT 1');
                $batch->execute([$medicineId]);
                $fefoBatch = $batch->fetch();
                if (!$fefoBatch) {
                    return;
                }

                $unit = (float)$medicine['selling_price'];
                $subtotal = $unit * $qty;
                $taxPercent = 12.0;
                $tax = $taxMode === 'inclusive' ? ($subtotal * $taxPercent / (100 + $taxPercent)) : ($subtotal * $taxPercent / 100);
                $grand = $taxMode === 'inclusive' ? $subtotal : ($subtotal + $tax);

                $sale = $db->prepare('INSERT INTO sales (company_id,patient_id,prescription_id,billed_by,payment_mode,tax_mode,subtotal,tax_total,grand_total) VALUES (?,?,?,?,?,?,?,?,?)');
                $sale->execute([$companyId, (int)($_POST['patient_id'] ?? 0) ?: null, (int)($_POST['prescription_id'] ?? 0) ?: null, (int)$user['id'], $payment, $taxMode, $subtotal, $tax, $grand]);
                $saleId = (int)$db->lastInsertId();

                $item = $db->prepare('INSERT INTO sale_items (sale_id,medicine_id,batch_id,qty,unit_price,tax_percent,line_total) VALUES (?,?,?,?,?,?,?)');
                $item->execute([$saleId, $medicineId, (int)$fefoBatch['id'], $qty, $unit, $taxPercent, $grand]);

                $deduct = $db->prepare('UPDATE medicine_batches SET qty = GREATEST(0, qty - ?) WHERE id=?');
                $deduct->execute([$qty, (int)$fefoBatch['id']]);

                if (!empty($_POST['patient_id'])) {
                    $loyalty = $db->prepare('UPDATE patients SET loyalty_points = loyalty_points + ? WHERE id=?');
                    $loyalty->execute([(int)floor($grand / 100), (int)$_POST['patient_id']]);
                }

                AuditService::log($companyId, (int)$user['id'], 'create', 'sale', $saleId, ['payment' => $payment]);
            }

            return;
        }

        if ($module === 'referrals') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'pharmacist']);
            if ($action === 'create_doctor') {
                $stmt = $db->prepare('INSERT INTO doctors (company_id,full_name,reg_number,specialization,phone,email) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$companyId, $_POST['full_name'], $_POST['reg_number'], $_POST['specialization'], $_POST['phone'], $_POST['email']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'doctor', (int)$db->lastInsertId());
            }

            if ($action === 'create_prescription') {
                $stmt = $db->prepare('INSERT INTO prescriptions (company_id,patient_id,doctor_id,source,notes,created_by) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$companyId, (int)$_POST['patient_id'], (int)$_POST['doctor_id'] ?: null, $_POST['source'], $_POST['notes'], (int)$user['id']]);
                $prescriptionId = (int)$db->lastInsertId();

                $item = $db->prepare('INSERT INTO prescription_items (prescription_id,medicine_id,dosage,duration_days,qty) VALUES (?,?,?,?,?)');
                $item->execute([$prescriptionId, (int)$_POST['medicine_id'], $_POST['dosage'], (int)$_POST['duration_days'], (int)$_POST['qty']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'prescription', $prescriptionId);
            }

            return;
        }

        if ($module === 'patients') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'pharmacist', 'billing']);
            if ($action === 'create_patient') {
                $stmt = $db->prepare('INSERT INTO patients (company_id,full_name,phone,email,dob,chronic_conditions,allergies) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$companyId, $_POST['full_name'], $_POST['phone'], $_POST['email'], $_POST['dob'] ?: null, $_POST['chronic_conditions'], $_POST['allergies']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'patient', (int)$db->lastInsertId());
            }

            if ($action === 'create_refill') {
                $stmt = $db->prepare('INSERT INTO refill_reminders (company_id,patient_id,medicine_id,next_refill_date,mode,sent) VALUES (?,?,?,?,?,0)');
                $stmt->execute([$companyId, (int)$_POST['patient_id'], (int)$_POST['medicine_id'], $_POST['next_refill_date'], $_POST['mode']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'refill_reminder', (int)$db->lastInsertId());
            }

            return;
        }

        if ($module === 'safety') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'pharmacist']);
            if ($action === 'create_interaction') {
                $stmt = $db->prepare('INSERT INTO drug_interactions (medicine_a_id,medicine_b_id,severity,reaction_notes) VALUES (?,?,?,?)');
                $stmt->execute([(int)$_POST['medicine_a_id'], (int)$_POST['medicine_b_id'], $_POST['severity'], $_POST['reaction_notes']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'drug_interaction', (int)$db->lastInsertId());
            }
            if ($action === 'add_narcotic_log') {
                $stmt = $db->prepare('INSERT INTO narcotics_log (company_id,medicine_id,action_type,qty,notes,created_by) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$companyId, (int)$_POST['medicine_id'], $_POST['action_type'], (int)$_POST['qty'], $_POST['notes'], (int)$user['id']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'narcotics_log', (int)$db->lastInsertId());
            }
            return;
        }

        if ($module === 'operations') {
            $this->assertRole($user, ['admin', 'owner', 'manager', 'pharmacist']);
            if ($action === 'add_cold_chain') {
                $stmt = $db->prepare('INSERT INTO cold_chain_logs (company_id,fridge_name,temperature_c,notes,logged_by) VALUES (?,?,?,?,?)');
                $stmt->execute([$companyId, $_POST['fridge_name'], (float)$_POST['temperature_c'], $_POST['notes'], (int)$user['id']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'cold_chain_log', (int)$db->lastInsertId());
            }
            if ($action === 'add_return') {
                $stmt = $db->prepare('INSERT INTO returns_to_supplier (company_id,supplier_id,medicine_id,batch_id,qty,reason,credit_amount) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$companyId, (int)$_POST['supplier_id'], (int)$_POST['medicine_id'], (int)$_POST['batch_id'] ?: null, (int)$_POST['qty'], $_POST['reason'], (float)$_POST['credit_amount']]);
                AuditService::log($companyId, (int)$user['id'], 'create', 'return_to_supplier', (int)$db->lastInsertId());
            }
            return;
        }

        if ($module === 'admin') {
            $this->assertRole($user, ['admin']);
            if ($action === 'create_company_user') {
                $db->beginTransaction();
                $companyStmt = $db->prepare('INSERT INTO companies (name,contact_email,phone) VALUES (?,?,?)');
                $companyStmt->execute([$_POST['company_name'], $_POST['company_email'], $_POST['company_phone']]);
                $cid = (int)$db->lastInsertId();

                $userStmt = $db->prepare('INSERT INTO users (company_id,username,email,password_hash,role,full_name,active) VALUES (?,?,?,?,?,?,1)');
                $userStmt->execute([
                    $cid,
                    $_POST['owner_username'],
                    $_POST['owner_email'],
                    password_hash((string)$_POST['owner_password'], PASSWORD_DEFAULT),
                    'owner',
                    $_POST['owner_name'],
                ]);
                $uid = (int)$db->lastInsertId();
                $db->commit();

                AuditService::log($cid, (int)$user['id'], 'create', 'company', $cid, ['owner_user_id' => $uid]);
            }
            return;
        }
    }

    private function loadModuleData(string $module, array $user, \PDO $db): array
    {
        $companyId = $this->companyId($user);

        if ($module === 'inventory') {
            $where = $user['role'] === 'admin' ? '' : 'WHERE m.company_id = :company_id';

            $medSql = "SELECT m.*, s.name supplier_name, COALESCE(SUM(b.qty),0) stock_qty
                       FROM medicines m
                       LEFT JOIN suppliers s ON s.id=m.supplier_id
                       LEFT JOIN medicine_batches b ON b.medicine_id=m.id
                       $where
                       GROUP BY m.id
                       ORDER BY m.created_at DESC";
            $medStmt = $db->prepare($medSql);
            if ($user['role'] !== 'admin') {
                $medStmt->bindValue(':company_id', $companyId, \PDO::PARAM_INT);
            }
            $medStmt->execute();

            $supStmt = $db->prepare('SELECT * FROM suppliers WHERE company_id = ? ORDER BY id DESC');
            $supStmt->execute([$companyId]);

            $batchStmt = $db->prepare("SELECT m.name, b.* FROM medicine_batches b JOIN medicines m ON m.id=b.medicine_id WHERE m.company_id=? ORDER BY b.expiry_date ASC");
            $batchStmt->execute([$companyId]);

            $alertsStmt = $db->prepare("SELECT m.name, b.batch_number, b.expiry_date, b.qty FROM medicine_batches b JOIN medicines m ON m.id=b.medicine_id WHERE m.company_id=? AND b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY b.expiry_date ASC");
            $alertsStmt->execute([$companyId]);

            return [
                'suppliers' => $supStmt->fetchAll(),
                'medicines' => $medStmt->fetchAll(),
                'batches' => $batchStmt->fetchAll(),
                'expiry_alerts' => $alertsStmt->fetchAll(),
            ];
        }

        if ($module === 'pos') {
            $medStmt = $db->prepare('SELECT id,name,barcode,selling_price,hsn_code,allergy_tags FROM medicines WHERE company_id=? ORDER BY name');
            $medStmt->execute([$companyId]);

            $patients = $db->prepare('SELECT id,full_name,allergies FROM patients WHERE company_id=? ORDER BY full_name');
            $patients->execute([$companyId]);

            $sales = $db->prepare('SELECT s.*, p.full_name patient_name, u.username billed_user FROM sales s LEFT JOIN patients p ON p.id=s.patient_id JOIN users u ON u.id=s.billed_by WHERE s.company_id=? ORDER BY s.id DESC LIMIT 20');
            $sales->execute([$companyId]);

            return [
                'medicines' => $medStmt->fetchAll(),
                'patients' => $patients->fetchAll(),
                'sales' => $sales->fetchAll(),
            ];
        }

        if ($module === 'referrals') {
            $doctors = $db->prepare('SELECT * FROM doctors WHERE company_id=? ORDER BY id DESC');
            $doctors->execute([$companyId]);

            $patients = $db->prepare('SELECT id,full_name FROM patients WHERE company_id=? ORDER BY full_name');
            $patients->execute([$companyId]);

            $medicines = $db->prepare('SELECT id,name,therapeutic_class FROM medicines WHERE company_id=? ORDER BY name');
            $medicines->execute([$companyId]);

            $flow = $db->prepare("SELECT d.full_name, COUNT(p.id) rx_count
                                  FROM doctors d
                                  LEFT JOIN prescriptions p ON p.doctor_id=d.id
                                  WHERE d.company_id=?
                                  GROUP BY d.id
                                  ORDER BY rx_count DESC");
            $flow->execute([$companyId]);

            $classAnalytics = $db->prepare("SELECT d.full_name, m.therapeutic_class, COUNT(pi.id) total
                FROM prescription_items pi
                JOIN prescriptions p ON p.id=pi.prescription_id
                JOIN doctors d ON d.id=p.doctor_id
                JOIN medicines m ON m.id=pi.medicine_id
                WHERE p.company_id=?
                GROUP BY d.full_name, m.therapeutic_class
                ORDER BY total DESC
                LIMIT 20");
            $classAnalytics->execute([$companyId]);

            return [
                'doctors' => $doctors->fetchAll(),
                'patients' => $patients->fetchAll(),
                'medicines' => $medicines->fetchAll(),
                'flow' => $flow->fetchAll(),
                'class_analytics' => $classAnalytics->fetchAll(),
            ];
        }

        if ($module === 'patients') {
            $patients = $db->prepare('SELECT * FROM patients WHERE company_id=? ORDER BY id DESC');
            $patients->execute([$companyId]);

            $medicines = $db->prepare('SELECT id,name FROM medicines WHERE company_id=? ORDER BY name');
            $medicines->execute([$companyId]);

            $refills = $db->prepare('SELECT rr.*, p.full_name patient_name, m.name med_name FROM refill_reminders rr JOIN patients p ON p.id=rr.patient_id JOIN medicines m ON m.id=rr.medicine_id WHERE rr.company_id=? ORDER BY rr.next_refill_date ASC');
            $refills->execute([$companyId]);

            return [
                'patients' => $patients->fetchAll(),
                'medicines' => $medicines->fetchAll(),
                'refills' => $refills->fetchAll(),
            ];
        }

        if ($module === 'safety') {
            $medicines = $db->prepare('SELECT id,name,generic_name,narcotic FROM medicines WHERE company_id=? ORDER BY name');
            $medicines->execute([$companyId]);

            $interactions = $db->prepare('SELECT di.*, ma.name a_name, mb.name b_name FROM drug_interactions di JOIN medicines ma ON ma.id=di.medicine_a_id JOIN medicines mb ON mb.id=di.medicine_b_id ORDER BY di.id DESC LIMIT 30');
            $interactions->execute();

            $narcotics = $db->prepare('SELECT nl.*, m.name med_name, u.username actor FROM narcotics_log nl JOIN medicines m ON m.id=nl.medicine_id JOIN users u ON u.id=nl.created_by WHERE nl.company_id=? ORDER BY nl.id DESC LIMIT 30');
            $narcotics->execute([$companyId]);

            return [
                'medicines' => $medicines->fetchAll(),
                'interactions' => $interactions->fetchAll(),
                'narcotics' => $narcotics->fetchAll(),
            ];
        }

        if ($module === 'operations') {
            $suppliers = $db->prepare('SELECT id,name FROM suppliers WHERE company_id=?');
            $suppliers->execute([$companyId]);

            $medicines = $db->prepare('SELECT id,name FROM medicines WHERE company_id=?');
            $medicines->execute([$companyId]);

            $batches = $db->prepare('SELECT b.id, m.name, b.batch_number FROM medicine_batches b JOIN medicines m ON m.id=b.medicine_id WHERE m.company_id=?');
            $batches->execute([$companyId]);

            $cold = $db->prepare('SELECT c.*, u.username FROM cold_chain_logs c JOIN users u ON u.id=c.logged_by WHERE c.company_id=? ORDER BY c.id DESC LIMIT 20');
            $cold->execute([$companyId]);

            $returns = $db->prepare('SELECT r.*, s.name supplier_name, m.name med_name FROM returns_to_supplier r JOIN suppliers s ON s.id=r.supplier_id JOIN medicines m ON m.id=r.medicine_id WHERE r.company_id=? ORDER BY r.id DESC LIMIT 20');
            $returns->execute([$companyId]);

            return [
                'suppliers' => $suppliers->fetchAll(),
                'medicines' => $medicines->fetchAll(),
                'batches' => $batches->fetchAll(),
                'cold_chain' => $cold->fetchAll(),
                'returns' => $returns->fetchAll(),
            ];
        }

        if ($module === 'analytics') {
            $salesByMonth = $db->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') ym, SUM(grand_total) total FROM sales WHERE company_id=? GROUP BY ym ORDER BY ym DESC LIMIT 12");
            $salesByMonth->execute([$companyId]);

            $profitByClass = $db->prepare("SELECT m.therapeutic_class, SUM((si.unit_price - m.purchase_price) * si.qty) profit
                FROM sale_items si
                JOIN sales s ON s.id=si.sale_id
                JOIN medicines m ON m.id=si.medicine_id
                WHERE s.company_id=?
                GROUP BY m.therapeutic_class
                ORDER BY profit DESC");
            $profitByClass->execute([$companyId]);

            $audit = $db->prepare('SELECT a.*, u.username FROM audit_trails a LEFT JOIN users u ON u.id=a.user_id WHERE a.company_id=? OR a.company_id IS NULL ORDER BY a.id DESC LIMIT 40');
            $audit->execute([$companyId]);

            return [
                'sales_by_month' => $salesByMonth->fetchAll(),
                'profit_by_class' => $profitByClass->fetchAll(),
                'audit' => $audit->fetchAll(),
            ];
        }

        if ($module === 'admin') {
            $this->assertRole($user, ['admin']);
            $companies = $db->query('SELECT c.*, COUNT(u.id) users FROM companies c LEFT JOIN users u ON u.company_id=c.id GROUP BY c.id ORDER BY c.id DESC')->fetchAll();
            $users = $db->query('SELECT id,company_id,username,email,role,full_name,active FROM users ORDER BY id DESC LIMIT 50')->fetchAll();
            return [
                'companies' => $companies,
                'users' => $users,
            ];
        }

        return [];
    }
}
