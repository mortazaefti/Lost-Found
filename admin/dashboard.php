<?php
session_start();
include '../config/db.php';

// only admin can see this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// count stats
$total_users = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_items = $conn->query("SELECT COUNT(*) AS c FROM items")->fetch_assoc()['c'];
$lost_items = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type='lost'")->fetch_assoc()['c'];
$found_items = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type='found'")->fetch_assoc()['c'];
$returned_items = $conn->query("SELECT COUNT(*) AS c FROM items WHERE status='returned'")->fetch_assoc()['c'];

// get latest 8 items
$recent = $conn->query("SELECT items.*, users.name AS reporter
                        FROM items
                        JOIN users ON items.user_id = users.user_id
                        ORDER BY items.created_at DESC
                        LIMIT 8");

$base = '../';
include '../includes/header.php';
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Dashboard Overview</h1>
        <p class="text-slate-500 mt-1">System statistics and recent activity</p>
    </div>
</div>

<!-- admin nav buttons -->
<div class="grid grid-cols-3 gap-3 mb-8">
    <a href="items.php" class="group bg-white border border-slate-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-md transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-indigo-700 text-sm">Manage Items</div>
            <div class="text-xs text-slate-500">Review and moderate reports</div>
        </div>
    </a>
    <a href="users.php" class="group bg-white border border-slate-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-md transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-indigo-700 text-sm">Manage Users</div>
            <div class="text-xs text-slate-500">Roles and access</div>
        </div>
    </a>
    <a href="categories.php" class="group bg-white border border-slate-200 rounded-xl p-4 hover:border-indigo-300 hover:shadow-md transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-indigo-700 text-sm">Categories & Locations</div>
            <div class="text-xs text-slate-500">Reference data</div>
        </div>
    </a>
</div>

<!-- stats boxes -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Users</div>
        <div class="text-2xl font-bold text-slate-900 mt-2"><?php echo $total_users; ?></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Total Items</div>
        <div class="text-2xl font-bold text-slate-900 mt-2"><?php echo $total_items; ?></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Lost</div>
        <div class="text-2xl font-bold text-rose-600 mt-2"><?php echo $lost_items; ?></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Found</div>
        <div class="text-2xl font-bold text-emerald-600 mt-2"><?php echo $found_items; ?></div>
    </div>
    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Returned</div>
        <div class="text-2xl font-bold text-indigo-600 mt-2"><?php echo $returned_items; ?></div>
    </div>
</div>

<!-- recent items table -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="font-semibold text-slate-900">Recent Activity</h2>
        <p class="text-xs text-slate-500 mt-0.5">Latest 8 items reported</p>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr class="text-slate-600 text-xs uppercase tracking-wider">
                <th class="px-6 py-3 text-left font-semibold">Title</th>
                <th class="px-6 py-3 text-left font-semibold">Type</th>
                <th class="px-6 py-3 text-left font-semibold">Status</th>
                <th class="px-6 py-3 text-left font-semibold">Reporter</th>
                <th class="px-6 py-3 text-right font-semibold">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        <?php while ($row = $recent->fetch_assoc()) { ?>
        <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-900"><?php echo $row['title']; ?></td>
            <td class="px-6 py-3">
                <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase <?php echo $row['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>"><?php echo $row['type']; ?></span>
            </td>
            <td class="px-6 py-3 text-slate-600"><?php echo $row['status']; ?></td>
            <td class="px-6 py-3 text-slate-600"><?php echo $row['reporter']; ?></td>
            <td class="px-6 py-3 text-right"><a href="../item.php?id=<?php echo $row['item_id']; ?>" class="text-indigo-600 font-medium hover:text-indigo-700 text-xs">View &rarr;</a></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
