<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Login Required");
}

if ($_SESSION['role_id'] != 3) {
    die("Access Denied - Managers Only");
}

$stmt = $pdo->prepare("
SELECT
    a.id AS approval_id,
    a.decision,
    q.id AS quotation_id,
    q.quotation_no,
    q.grand_total,
    q.delivery_timeline,
    v.company_name
FROM approvals a
JOIN quotations q ON a.quotation_id = q.id
JOIN vendors v ON q.vendor_id = v.id
WHERE a.approver_id = ?
ORDER BY a.created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);

$approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manager Approvals</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
    background:#050505;
    color:white;
}
.glass-card{
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.1);
    backdrop-filter:blur(10px);
}
</style>

</head>

<body class="flex">
     <aside class="w-64 min-h-screen p-6 border-r border-white/10 flex flex-col justify-between fixed left-0 top-0 h-full bg-[#050505]">
        <div>
            <h1 class="text-2xl font-black mb-10 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 tracking-wide">VENDORBRIDGE</h1>
          <nav class="space-y-2">
    <a href="../Frontend/index.html" id="nav-dashboard" class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">Dashboard</a>
    <a href="../Frontend/vendors.html" id="nav-vendors" class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">Vendors Matrix</a>
    
    <div class="mt-6">
        <p class="px-4 text-[10px] uppercase tracking-widest text-slate-500 font-bold mb-2">RFQ's</p>
        <a href="../Frontend/create_rfq.html" id="nav-create" class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">Create New</a>
       
    </div>

    <a href="../Frontend/submit_quotation.html" id="nav-submit" class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">Submit Quotation</a>
    <a href="../backend/comparison.php"
   id="nav-comparison"
   class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
   Compare Quotations
</a>
<a href="../backend/approvals.php"
   id="nav-approvals"
   class="nav-link block py-3 px-4 rounded-xl text-blue-400 border border-blue-500/50 bg-blue-600/20 font-semibold">
   Manager Approvals
</a>
<a href="../backend/purchase_orders.php"
   id="nav-po"
   class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
   Purchase Orders
</a>
    <a href="../Frontend/invoices.html" id="nav-invoices" class="nav-link block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">Invoice Ledger</a>
</nav>
        </div>
        <a href="../Frontend/login.html" class="block py-3 px-4 rounded-xl text-red-400 hover:bg-red-900/20 border border-red-900/50 transition text-center text-sm font-bold">Logout</a>
    </aside>

<div class="flex-1 pl-64 w-full">
    <main class="p-10">

        <header class="mb-8">
            <h1 class="text-3xl font-black text-white">
                Manager Approvals
            </h1>
            <p class="text-slate-400 mt-2">
                Review and process shortlisted vendor quotations.
            </p>
        </header>
<div class="grid grid-cols-4 gap-6 mb-8">

    <div class="glass-card p-6 rounded-3xl border-l-4 border-yellow-500">
        <p class="text-slate-400 text-xs uppercase font-bold">
            Pending
        </p>
        <h3 class="text-3xl font-black mt-2">
            <?= count(array_filter($approvals, fn($a) => $a['decision'] == 'pending')) ?>
        </h3>
    </div>

    <div class="glass-card p-6 rounded-3xl border-l-4 border-emerald-500">
        <p class="text-slate-400 text-xs uppercase font-bold">
            Approved
        </p>
        <h3 class="text-3xl font-black mt-2">
            <?= count(array_filter($approvals, fn($a) => $a['decision'] == 'approved')) ?>
        </h3>
    </div>

    <div class="glass-card p-6 rounded-3xl border-l-4 border-red-500">
        <p class="text-slate-400 text-xs uppercase font-bold">
            Rejected
        </p>
        <h3 class="text-3xl font-black mt-2">
            <?= count(array_filter($approvals, fn($a) => $a['decision'] == 'rejected')) ?>
        </h3>
    </div>

    <div class="glass-card p-6 rounded-3xl border-l-4 border-blue-500">
        <p class="text-slate-400 text-xs uppercase font-bold">
            Total Requests
        </p>
        <h3 class="text-3xl font-black mt-2">
            <?= count($approvals) ?>
        </h3>
    </div>

</div>
        <div class="max-w-7xl mx-auto">



<div class="glass-card rounded-3xl overflow-hidden">

<table class="w-full">

<thead class="border-b border-white/10">
<tr class="text-slate-400 uppercase text-xs">

<th class="p-6 text-left">Vendor</th>
<th class="p-6 text-left">Quotation</th>
<th class="p-6 text-left">Amount</th>
<th class="p-6 text-left">Delivery</th>
<th class="p-6 text-left">Status</th>
<th class="p-6 text-left">Action</th>

</tr>
</thead>

<tbody>
<?php foreach($approvals as $row): ?>

<tr class="border-b border-white/5">

<td class="p-6">
<?= htmlspecialchars($row['company_name']) ?>
</td>

<td class="p-6">
<?= htmlspecialchars($row['quotation_no']) ?>
</td>

<td class="p-6 text-emerald-400 font-bold">
₹<?= number_format($row['grand_total'],2) ?>
</td>

<td class="p-6">
<?= htmlspecialchars($row['delivery_timeline']) ?>
</td>

<td class="p-6">

<?php if($row['decision']=='pending'): ?>

<span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400">
Pending
</span>

<?php elseif($row['decision']=='approved'): ?>

<span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
Approved
</span>

<?php else: ?>

<span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400">
Rejected
</span>

<?php endif; ?>

</td>

<td class="p-6">

<?php if($row['decision']=='pending'): ?>

<form action="process_approval.php" method="POST" class="flex gap-2">

<input type="hidden"
name="approval_id"
value="<?= $row['approval_id'] ?>">

<input type="hidden"
name="quotation_id"
value="<?= $row['quotation_id'] ?>">

<button
name="action"
value="approved"
class="bg-emerald-600 px-4 py-2 rounded-xl font-bold">

Approve

</button>

<button
name="action"
value="rejected"
class="bg-red-600 px-4 py-2 rounded-xl font-bold">

Reject

</button>

</form>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>
</tbody>
</table>
</div>
        </div>

    </main>
</div>

</body>
</html>