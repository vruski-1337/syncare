<div class="container">
    <h1>Welcome, <?= e($user['full_name']) ?></h1>
    <p class="muted">Role: <?= e($user['role']) ?><?= $user['company_id'] ? ' | Company ID: ' . e((string)$user['company_id']) : '' ?></p>

    <div class="grid grid-4">
        <div class="card"><div class="muted">Companies</div><div class="kpi"><?= e((string)($kpi['companies'] ?? 0)) ?></div></div>
        <div class="card"><div class="muted">Users / Patients</div><div class="kpi"><?= e((string)($kpi['users'] ?? 0)) ?></div></div>
        <div class="card"><div class="muted">Total Sales</div><div class="kpi">₹<?= e(number_format((float)($kpi['sales'] ?? 0),2)) ?></div></div>
        <div class="card"><div class="muted">Expiry Alerts</div><div class="kpi"><?= e((string)($kpi['expiry_alerts'] ?? 0)) ?></div></div>
    </div>

    <div class="grid grid-3" style="margin-top:16px;">
        <a class="card" href="/module/inventory" style="text-decoration:none;color:inherit;"><h3>Inventory & Stock</h3><p class="muted">Stock zones, FEFO, expiry, smart reorder.</p></a>
        <a class="card" href="/module/pos" style="text-decoration:none;color:inherit;"><h3>Prescription & POS</h3><p class="muted">Barcode checkout, GST, payment modes.</p></a>
        <a class="card" href="/module/referrals" style="text-decoration:none;color:inherit;"><h3>Doctor Network</h3><p class="muted">Referrals, physician registry, Rx linkage.</p></a>
        <a class="card" href="/module/patients" style="text-decoration:none;color:inherit;"><h3>Patient CRM</h3><p class="muted">Profiles, refill reminders, allergy data.</p></a>
        <a class="card" href="/module/safety" style="text-decoration:none;color:inherit;"><h3>Clinical Safety</h3><p class="muted">Interactions, narcotics, dose checks.</p></a>
        <a class="card" href="/module/operations" style="text-decoration:none;color:inherit;"><h3>Operations</h3><p class="muted">Cold chain, returns, supply control.</p></a>
    </div>
</div>
