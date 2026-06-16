<?php

session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Login Required");
}

$stmt = $pdo->query("
SELECT
    po.id,
    po.po_no,
    po.issue_date,
    po.total_amount,
    po.status,
    v.company_name
FROM purchase_orders po
JOIN vendors v ON po.vendor_id = v.id
ORDER BY po.created_at DESC
");

$purchaseOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html>
<head>

<meta charset="UTF-8">
<title>Purchase Orders</title>

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

```
<div>

    <h1 class="text-2xl font-black mb-10 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 tracking-wide">
        VENDORBRIDGE
    </h1>

    <nav class="space-y-2">

        <a href="../frontend/index.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Dashboard
        </a>

        <a href="../frontend/vendors.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Vendors Matrix
        </a>

        <a href="../frontend/create_rfq.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Create New RFQ
        </a>

        <a href="../backend/comparison.php"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Compare Quotations
        </a>

        <a href="../backend/approvals.php"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Manager Approvals
        </a>

        <a href="../backend/purchase_orders.php"
           class="block py-3 px-4 rounded-xl text-blue-400 border border-blue-500/50 bg-blue-600/20 font-semibold">
           Purchase Orders
        </a>

        <a href="../frontend/invoices.html"
           class="block py-3 px-4 rounded-xl text-slate-400 hover:text-white transition font-semibold">
           Invoice Ledger
        </a>

    </nav>

</div>

<a href="../frontend/login.html"
   class="block py-3 px-4 rounded-xl text-red-400 hover:bg-red-900/20 border border-red-900/50 transition text-center text-sm font-bold">
   Logout
</a>
```

</aside>

<div class="flex-1 pl-64 w-full">

```
<main class="p-10">

    <header class="mb-8">

        <h1 class="text-3xl font-black">
            Purchase Orders
        </h1>

        <p class="text-slate-400 mt-2">
            Generated purchase orders after management approval.
        </p>

    </header>

    <div class="glass-card rounded-3xl overflow-hidden">

        <table class="w-full">

            <thead class="border-b border-white/10">

                <tr class="text-slate-400 uppercase text-xs">

                    <th class="p-6 text-left">PO Number</th>
                    <th class="p-6 text-left">Vendor</th>
                    <th class="p-6 text-left">Issue Date</th>
                    <th class="p-6 text-left">Amount</th>
                    <th class="p-6 text-left">Status</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach($purchaseOrders as $po): ?>

            <tr class="border-b border-white/5">

                <td class="p-6 font-semibold text-blue-400">
                    <?= htmlspecialchars($po['po_no']) ?>
                </td>

                <td class="p-6">
                    <?= htmlspecialchars($po['company_name']) ?>
                </td>

                <td class="p-6">
                    <?= htmlspecialchars($po['issue_date']) ?>
                </td>

                <td class="p-6 text-emerald-400 font-bold">
                    ₹<?= number_format($po['total_amount'],2) ?>
                </td>

                <td class="p-6">

                    <?php if($po['status']=='generated'): ?>

                    <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400">
                        Generated
                    </span>

                    <?php elseif($po['status']=='sent'): ?>

                    <span class="px-3 py-1 rounded-full bg-yellow-500/20 text-yellow-400">
                        Sent
                    </span>

                    <?php elseif($po['status']=='completed'): ?>

                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
                        Completed
                    </span>

                    <?php else: ?>

                    <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-400">
                        Cancelled
                    </span>

                    <?php endif; ?>

                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</main>
```

</div>

</body>
</html>
