<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login Required");
}

if ($_SESSION['role_id'] != 2) {
    die("Access Denied");
}
?>
<?php

require_once 'db.php';

$rfq_id = $_GET['rfq_id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT
        q.id,
        q.quotation_no,
        q.grand_total,
        q.delivery_timeline,
        q.status,
        v.company_name,
        v.rating
    FROM quotations q
    JOIN vendors v ON q.vendor_id = v.id
    WHERE q.rfq_id = ?
");

$stmt->execute([$rfq_id]);

$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lowestPrice = PHP_FLOAT_MAX;

foreach ($quotations as $q) {
    if ($q['grand_total'] < $lowestPrice) {
        $lowestPrice = $q['grand_total'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quotation Comparison - VendorBridge</title>

<script src="https://cdn.tailwindcss.com"></script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

<style>
body{
    background:#050505;
    color:white;
    font-family:'Inter',sans-serif;
}

.glass-card{
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.1);
    backdrop-filter:blur(10px);
}
</style>

</head>

<body class="flex">

<!-- SIDEBAR -->

<aside class="w-64 min-h-screen p-6 border-r border-white/10 fixed left-0 top-0 bg-[#050505]">

    <h1 class="text-2xl font-black mb-10 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
        VENDORBRIDGE
    </h1>

    <nav class="space-y-2">

        <a href="../frontend/index.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition">
            Dashboard
        </a>

        <a href="../frontend/vendors.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition">
            Vendors Matrix
        </a>

        <a href="../frontend/create_rfq.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition">
            Create RFQ
        </a>

        <a href="../frontend/submit_quotation.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition">
            Submit Quotation
        </a>

        <a href="#"
           class="block py-3 px-4 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/50">
            Compare Quotations
        </a>

    </nav>

</aside>

<!-- MAIN CONTENT -->

<div class="flex-1 pl-72 p-10">

    <div class="mb-8">

        <h1 class="text-3xl font-black">
            Quotation Comparison
        </h1>

        <p class="text-slate-400 mt-2">
            Compare vendor proposals and shortlist the best quotation.
        </p>

    </div>

    <div class="glass-card rounded-3xl overflow-hidden">

        <table class="w-full">

            <thead class="border-b border-white/10">

                <tr class="text-slate-400 text-xs uppercase tracking-widest">

                    <th class="p-6 text-left">Vendor</th>
                    <th class="p-6 text-left">Quotation No</th>
                    <th class="p-6 text-left">Amount</th>
                    <th class="p-6 text-left">Delivery</th>
                    <th class="p-6 text-left">Rating</th>
                    <th class="p-6 text-left">Status</th>
                    <th class="p-6 text-left">Action</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach($quotations as $q): ?>

            <tr class="border-b border-white/5 <?= ($q['grand_total'] == $lowestPrice) ? 'bg-emerald-500/10' : '' ?>">

                <td class="p-6">

                    <div class="flex items-center gap-2">

                        <span>
                            <?= htmlspecialchars($q['company_name']) ?>
                        </span>

                        <?php if($q['grand_total'] == $lowestPrice): ?>
                            <span class="text-xs font-bold text-emerald-400">
                                LOWEST
                            </span>
                        <?php endif; ?>

                    </div>

                </td>

                <td class="p-6">
                    <?= htmlspecialchars($q['quotation_no']) ?>
                </td>

                <td class="p-6">

                    <span class="font-black text-emerald-400">
                        ₹<?= number_format($q['grand_total'],2) ?>
                    </span>

                </td>

                <td class="p-6">
                    <?= htmlspecialchars($q['delivery_timeline']) ?>
                </td>

                <td class="p-6">
                    ⭐ <?= $q['rating'] ?>
                </td>

                <td class="p-6">

                    <?php if($q['status']=='shortlisted'): ?>

                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">
                            SHORTLISTED
                        </span>

                    <?php else: ?>

                        <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-bold">
                            <?= strtoupper($q['status']) ?>
                        </span>

                    <?php endif; ?>

                </td>

                <td class="p-6">

                    <form action="shortlist_vendor.php" method="POST">

                        <input type="hidden"
                               name="quotation_id"
                               value="<?= $q['id'] ?>">

                        <input type="hidden"
                               name="rfq_id"
                               value="<?= $rfq_id ?>">

                        <button
                            class="bg-blue-600 hover:bg-blue-500 transition px-4 py-2 rounded-xl font-bold text-sm">

                            Shortlist

                        </button>

                    </form>

                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>