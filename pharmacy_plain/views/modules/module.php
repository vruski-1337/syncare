<div class="container">
    <h1><?= e(ucfirst($module)) ?></h1>

    <?php if ($module === 'inventory'): ?>
        <div class="grid grid-3">
            <div class="card">
                <h3>Supplier Directory</h3>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_supplier">
                    <input name="name" placeholder="Supplier name" required>
                    <input name="contact_person" placeholder="Contact person">
                    <input name="phone" placeholder="Phone">
                    <input name="email" placeholder="Email">
                    <input type="number" name="lead_time_days" placeholder="Lead time days" value="0">
                    <textarea name="notes" placeholder="Historical pricing / notes"></textarea>
                    <button>Add Supplier</button>
                </form>
            </div>
            <div class="card">
                <h3>Add Medicine</h3>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_medicine">
                    <input name="name" placeholder="Medicine name" required>
                    <input name="generic_name" placeholder="Generic name">
                    <input name="therapeutic_class" placeholder="Therapeutic class">
                    <input name="barcode" placeholder="Barcode">
                    <input name="hsn_code" placeholder="HSN code">
                    <input type="number" step="0.01" name="purchase_price" placeholder="Purchase price" required>
                    <input type="number" step="0.01" name="selling_price" placeholder="Selling price" required>
                    <input type="number" name="min_stock" placeholder="Min stock" required>
                    <input name="allergy_tags" placeholder="Allergy tags comma-separated">
                    <select name="supplier_id"><option value="">Supplier (optional)</option><?php foreach (($data['suppliers'] ?? []) as $s): ?><option value="<?= e((string)$s['id']) ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select>
                    <label><input type="checkbox" name="narcotic"> Narcotic medicine</label>
                    <button>Add Medicine</button>
                </form>
            </div>
            <div class="card">
                <h3>Add Batch</h3>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_batch">
                    <select name="medicine_id" required><option value="">Medicine</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <input name="batch_number" placeholder="Batch number" required>
                    <input name="storage_zone" placeholder="Storage zone (OPD/ER/Ward)" required>
                    <input type="number" name="qty" placeholder="Qty" required>
                    <input type="date" name="expiry_date" required>
                    <button>Add Batch</button>
                </form>
                <form method="post" style="margin-top:10px;">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="auto_reorder">
                    <button class="warn">Run Smart Reorder</button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top:16px;">
            <h3>Expiry Alerts (30 days)</h3>
            <table><tr><th>Medicine</th><th>Batch</th><th>Expiry</th><th>Qty</th></tr>
                <?php foreach (($data['expiry_alerts'] ?? []) as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['batch_number']) ?></td><td><?= e($r['expiry_date']) ?></td><td><?= e((string)$r['qty']) ?></td></tr><?php endforeach; ?>
            </table>
        </div>

        <div class="card" style="margin-top:16px;">
            <h3>Inventory (with FEFO-ready batches)</h3>
            <table><tr><th>Medicine</th><th>Generic</th><th>Supplier</th><th>Stock</th><th>Min</th><th>Barcode</th></tr>
                <?php foreach (($data['medicines'] ?? []) as $m): ?><tr><td><?= e($m['name']) ?></td><td><?= e((string)$m['generic_name']) ?></td><td><?= e((string)$m['supplier_name']) ?></td><td><?= e((string)$m['stock_qty']) ?></td><td><?= e((string)$m['min_stock']) ?></td><td><?= e((string)$m['barcode']) ?></td></tr><?php endforeach; ?>
            </table>
        </div>

    <?php elseif ($module === 'pos'): ?>
        <div class="grid grid-3">
            <div class="card">
                <h3>Universal Billing (POS)</h3>
                <form method="post">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_sale">
                    <select name="patient_id"><option value="">Walk-in patient</option><?php foreach (($data['patients'] ?? []) as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['full_name']) ?></option><?php endforeach; ?></select>
                    <select name="medicine_id" required><option value="">Medicine (or scan barcode first)</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?> | <?= e((string)$m['barcode']) ?></option><?php endforeach; ?></select>
                    <input type="number" name="qty" placeholder="Quantity" value="1" required>
                    <select name="payment_mode" required>
                        <option value="upi">UPI</option><option value="card">Card</option><option value="insurance">Insurance</option><option value="cash">Cash</option>
                    </select>
                    <select name="tax_mode" required><option value="exclusive">Tax Exclusive</option><option value="inclusive">Tax Inclusive</option></select>
                    <button>Checkout</button>
                </form>
            </div>
            <div class="card">
                <h3>Allergy Flagging Tip</h3>
                <p class="muted">Before billing, compare patient allergy list with medicine allergy tags shown in medicine master. Use this panel as your safety checkpoint.</p>
                <h3 style="margin-top:12px;">Barcode Index</h3>
                <table><tr><th>Barcode</th><th>Medicine</th></tr><?php foreach (($data['medicines'] ?? []) as $m): ?><tr><td><?= e((string)$m['barcode']) ?></td><td><?= e($m['name']) ?></td></tr><?php endforeach; ?></table>
            </div>
            <div class="card">
                <h3>Recent Bills</h3>
                <table><tr><th>ID</th><th>Patient</th><th>Payment</th><th>Total</th></tr><?php foreach (($data['sales'] ?? []) as $s): ?><tr><td>#<?= e((string)$s['id']) ?></td><td><?= e((string)$s['patient_name']) ?></td><td><?= e($s['payment_mode']) ?></td><td>₹<?= e(number_format((float)$s['grand_total'],2)) ?></td></tr><?php endforeach; ?></table>
            </div>
        </div>

    <?php elseif ($module === 'referrals'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Physician Database</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_doctor">
                    <input name="full_name" placeholder="Doctor name" required><input name="reg_number" placeholder="Registry number"><input name="specialization" placeholder="Specialization"><input name="phone" placeholder="Phone"><input name="email" placeholder="Email"><button>Add Doctor</button>
                </form>
            </div>
            <div class="card"><h3>Digital Prescription Entry</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_prescription">
                    <select name="patient_id" required><option value="">Patient</option><?php foreach (($data['patients'] ?? []) as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['full_name']) ?></option><?php endforeach; ?></select>
                    <select name="doctor_id"><option value="">Doctor (optional)</option><?php foreach (($data['doctors'] ?? []) as $d): ?><option value="<?= e((string)$d['id']) ?>"><?= e($d['full_name']) ?></option><?php endforeach; ?></select>
                    <select name="source"><option value="manual">Manual</option><option value="ocr">OCR Scan</option><option value="e_rx">E-Prescription Sync</option></select>
                    <select name="medicine_id" required><option value="">Medicine</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <input name="dosage" placeholder="Dosage e.g. 1-0-1"><input type="number" name="duration_days" placeholder="Duration days" value="5"><input type="number" name="qty" placeholder="Qty" value="10"><textarea name="notes" placeholder="Notes"></textarea><button>Create Prescription</button>
                </form>
            </div>
            <div class="card"><h3>Referral Dashboard</h3>
                <table><tr><th>Doctor</th><th>Rx Count</th></tr><?php foreach (($data['flow'] ?? []) as $r): ?><tr><td><?= e((string)$r['full_name']) ?></td><td><?= e((string)$r['rx_count']) ?></td></tr><?php endforeach; ?></table>
            </div>
        </div>
        <div class="card" style="margin-top:16px;"><h3>Collaboration Analytics (Doctor × Therapeutic Class)</h3>
            <table><tr><th>Doctor</th><th>Class</th><th>Total Rx Items</th></tr><?php foreach (($data['class_analytics'] ?? []) as $r): ?><tr><td><?= e((string)$r['full_name']) ?></td><td><?= e((string)$r['therapeutic_class']) ?></td><td><?= e((string)$r['total']) ?></td></tr><?php endforeach; ?></table>
        </div>

    <?php elseif ($module === 'patients'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Patient Profiles</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_patient">
                    <input name="full_name" placeholder="Full name" required><input name="phone" placeholder="Phone"><input name="email" placeholder="Email"><input type="date" name="dob"><textarea name="chronic_conditions" placeholder="Chronic conditions"></textarea><textarea name="allergies" placeholder="Allergies"></textarea><button>Add Patient</button>
                </form>
            </div>
            <div class="card"><h3>Refill Automation</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_refill">
                    <select name="patient_id" required><option value="">Patient</option><?php foreach (($data['patients'] ?? []) as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['full_name']) ?></option><?php endforeach; ?></select>
                    <select name="medicine_id" required><option value="">Medicine</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <input type="date" name="next_refill_date" required>
                    <select name="mode"><option value="sms">SMS</option><option value="email">Email</option></select>
                    <button>Schedule Reminder</button>
                </form>
            </div>
            <div class="card"><h3>Loyalty Snapshot</h3>
                <table><tr><th>Patient</th><th>Points</th><th>Allergies</th></tr><?php foreach (($data['patients'] ?? []) as $p): ?><tr><td><?= e($p['full_name']) ?></td><td><?= e((string)$p['loyalty_points']) ?></td><td><?= e((string)$p['allergies']) ?></td></tr><?php endforeach; ?></table>
            </div>
        </div>

    <?php elseif ($module === 'safety'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Drug Interaction Checker</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_interaction">
                    <select name="medicine_a_id" required><option value="">Medicine A</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <select name="medicine_b_id" required><option value="">Medicine B</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <select name="severity"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
                    <textarea name="reaction_notes" placeholder="Reaction notes / food interactions"></textarea><button>Add Interaction Rule</button>
                </form>
                <p class="small">Dose verification quick check: Pediatric dose = Adult dose × (age/age+12)</p>
            </div>
            <div class="card"><h3>Narcotics Log</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add_narcotic_log">
                    <select name="medicine_id" required><option value="">Narcotic medicine</option><?php foreach (($data['medicines'] ?? []) as $m): if ((int)$m['narcotic'] !== 1) continue; ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <select name="action_type"><option value="in">IN</option><option value="out">OUT</option><option value="adjust">ADJUST</option></select>
                    <input type="number" name="qty" placeholder="Qty" required>
                    <textarea name="notes" placeholder="Legal notes"></textarea><button>Log Entry</button>
                </form>
            </div>
            <div class="card"><h3>Generic Substitution</h3>
                <table><tr><th>Medicine</th><th>Generic</th></tr><?php foreach (($data['medicines'] ?? []) as $m): ?><tr><td><?= e($m['name']) ?></td><td><?= e((string)$m['generic_name']) ?></td></tr><?php endforeach; ?></table>
            </div>
        </div>
        <div class="card" style="margin-top:16px;"><h3>Interaction Rules</h3><table><tr><th>A</th><th>B</th><th>Severity</th><th>Notes</th></tr><?php foreach (($data['interactions'] ?? []) as $r): ?><tr><td><?= e($r['a_name']) ?></td><td><?= e($r['b_name']) ?></td><td><?= e($r['severity']) ?></td><td><?= e((string)$r['reaction_notes']) ?></td></tr><?php endforeach; ?></table></div>

    <?php elseif ($module === 'operations'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Cold Chain Logs</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add_cold_chain">
                    <input name="fridge_name" placeholder="Fridge name" required><input type="number" step="0.01" name="temperature_c" placeholder="Temperature °C" required><textarea name="notes" placeholder="Notes"></textarea><button>Add Temperature Log</button>
                </form>
            </div>
            <div class="card"><h3>Purchase Returns</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add_return">
                    <select name="supplier_id" required><option value="">Supplier</option><?php foreach (($data['suppliers'] ?? []) as $s): ?><option value="<?= e((string)$s['id']) ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select>
                    <select name="medicine_id" required><option value="">Medicine</option><?php foreach (($data['medicines'] ?? []) as $m): ?><option value="<?= e((string)$m['id']) ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select>
                    <select name="batch_id"><option value="">Batch (optional)</option><?php foreach (($data['batches'] ?? []) as $b): ?><option value="<?= e((string)$b['id']) ?>"><?= e($b['name']) ?> | <?= e($b['batch_number']) ?></option><?php endforeach; ?></select>
                    <input type="number" name="qty" placeholder="Qty" required><input name="reason" placeholder="Reason"><input type="number" step="0.01" name="credit_amount" placeholder="Credit amount"><button>Log Return</button>
                </form>
            </div>
            <div class="card"><h3>Multi-Store Sync</h3><p class="muted">Use storage zones in inventory batches (OPD/ER/Wards) to centrally monitor distributed stock.</p></div>
        </div>

    <?php elseif ($module === 'analytics'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Sales Forecasting Input</h3><p class="muted">Monthly sales trend for seasonal planning.</p><table><tr><th>Month</th><th>Sales</th></tr><?php foreach (($data['sales_by_month'] ?? []) as $r): ?><tr><td><?= e($r['ym']) ?></td><td>₹<?= e(number_format((float)$r['total'],2)) ?></td></tr><?php endforeach; ?></table></div>
            <div class="card"><h3>Profitability Reports</h3>
                <?php if (($user['role'] ?? '') === 'owner' || ($user['role'] ?? '') === 'admin'): ?>
                    <table><tr><th>Therapeutic Class</th><th>Profit</th></tr><?php foreach (($data['profit_by_class'] ?? []) as $r): ?><tr><td><?= e((string)$r['therapeutic_class']) ?></td><td>₹<?= e(number_format((float)$r['profit'],2)) ?></td></tr><?php endforeach; ?></table>
                <?php else: ?>
                    <div class="tag warn">Profit view is restricted to Owner/Admin only.</div>
                <?php endif; ?>
            </div>
            <div class="card"><h3>Audit Trail (non-editable)</h3><table><tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th></tr><?php foreach (($data['audit'] ?? []) as $a): ?><tr><td><?= e((string)$a['created_at']) ?></td><td><?= e((string)$a['username']) ?></td><td><?= e($a['action']) ?></td><td><?= e($a['entity']) ?></td></tr><?php endforeach; ?></table></div>
        </div>

    <?php elseif ($module === 'admin'): ?>
        <div class="grid grid-3">
            <div class="card"><h3>Create Company + Owner Credentials</h3>
                <form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_company_user">
                    <input name="company_name" placeholder="Company name" required>
                    <input name="company_email" placeholder="Company email" required>
                    <input name="company_phone" placeholder="Company phone">
                    <input name="owner_name" placeholder="Owner full name" required>
                    <input name="owner_username" placeholder="Owner username" required>
                    <input name="owner_email" placeholder="Owner email" required>
                    <input type="password" name="owner_password" placeholder="Owner password" required>
                    <button>Create Company Account</button>
                </form>
            </div>
            <div class="card" style="grid-column: span 2;"><h3>Companies</h3><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Users</th></tr><?php foreach (($data['companies'] ?? []) as $c): ?><tr><td><?= e((string)$c['id']) ?></td><td><?= e($c['name']) ?></td><td><?= e((string)$c['contact_email']) ?></td><td><?= e((string)$c['users']) ?></td></tr><?php endforeach; ?></table></div>
        </div>
        <div class="card" style="margin-top:16px;"><h3>User Access Control</h3><table><tr><th>ID</th><th>Company</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr><?php foreach (($data['users'] ?? []) as $u): ?><tr><td><?= e((string)$u['id']) ?></td><td><?= e((string)$u['company_id']) ?></td><td><?= e($u['username']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= (int)$u['active']===1?'Active':'Disabled' ?></td></tr><?php endforeach; ?></table></div>

    <?php else: ?>
        <div class="card">Unknown module.</div>
    <?php endif; ?>
</div>
