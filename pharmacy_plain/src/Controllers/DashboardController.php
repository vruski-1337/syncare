<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

final class DashboardController
{
    public function index(): void
    {
        $user = Auth::user();
        if (!$user) {
            redirect('/login');
        }

        $db = Database::connection();
        $companyId = (int)($user['company_id'] ?? 0);

        if ($user['role'] === 'admin') {
            $kpi = [
                'companies' => (int)$db->query('SELECT COUNT(*) FROM companies')->fetchColumn(),
                'users' => (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
                'sales' => (float)$db->query('SELECT COALESCE(SUM(grand_total),0) FROM sales')->fetchColumn(),
                'expiry_alerts' => (int)$db->query("SELECT COUNT(*) FROM medicine_batches WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn(),
            ];
        } else {
            $stmtA = $db->prepare("SELECT COUNT(*) FROM medicine_batches mb JOIN medicines m ON m.id=mb.medicine_id WHERE m.company_id=? AND mb.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
            $stmtA->execute([$companyId]);

            $stmtS = $db->prepare('SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE company_id=?');
            $stmtS->execute([$companyId]);

            $stmtP = $db->prepare('SELECT COUNT(*) FROM patients WHERE company_id=?');
            $stmtP->execute([$companyId]);

            $stmtM = $db->prepare('SELECT COUNT(*) FROM medicines WHERE company_id=?');
            $stmtM->execute([$companyId]);

            $kpi = [
                'companies' => 1,
                'users' => (int)$stmtP->fetchColumn(),
                'sales' => (float)$stmtS->fetchColumn(),
                'expiry_alerts' => (int)$stmtA->fetchColumn(),
                'medicines' => (int)$stmtM->fetchColumn(),
            ];
        }

        View::render('dashboard/index', [
            'user' => $user,
            'kpi' => $kpi,
        ]);
    }
}
