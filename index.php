<?php
session_start();
include 'config/db.php';

// get latest 6 open items for homepage
$sql = "SELECT items.*, categories.name AS category_name, locations.name AS location_name
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN locations ON items.location_id = locations.location_id
        WHERE items.status = 'open'
        ORDER BY items.created_at DESC
        LIMIT 6";
$result = $conn->query($sql);

// count stats for the boxes
$lost = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type='lost'")->fetch_assoc()['c'];
$found = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type='found'")->fetch_assoc()['c'];
$returned = $conn->query("SELECT COUNT(*) AS c FROM items WHERE status='returned'")->fetch_assoc()['c'];

include 'includes/header.php';
?>

<!-- hero section -->
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white p-10 mb-10 shadow-lg">
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 20%, white 2px, transparent 2px), radial-gradient(circle at 80% 60%, white 2px, transparent 2px); background-size: 60px 60px;"></div>
    <div class="relative max-w-2xl">
        <h1 class="text-4xl font-extrabold tracking-tight mb-3">Lost something on campus?</h1>
        <p class="text-indigo-100 text-lg mb-6">Report it here and help reunite belongings with their owners. Search found items posted by other students.</p>
        <div class="flex flex-wrap gap-3">
            <a href="report_lost.php" class="inline-flex items-center gap-2 bg-white text-indigo-700 px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Report Lost
            </a>
            <a href="report_found.php" class="inline-flex items-center gap-2 bg-amber-400 text-slate-900 px-5 py-2.5 rounded-lg font-semibold hover:bg-amber-300 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Report Found
            </a>
            <a href="search.php" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-white/20 backdrop-blur">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                Search Items
            </a>
        </div>
    </div>
</div>

<!-- stats boxes -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
    <div class="bg-white p-6 rounded-xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <div class="text-3xl font-bold text-slate-900"><?php echo $lost; ?></div>
            <div class="text-sm text-slate-500">Lost Items Reported</div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="text-3xl font-bold text-slate-900"><?php echo $found; ?></div>
            <div class="text-sm text-slate-500">Found Items Posted</div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-200 flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
            <div class="text-3xl font-bold text-slate-900"><?php echo $returned; ?></div>
            <div class="text-sm text-slate-500">Returned to Owner</div>
        </div>
    </div>
</div>

<!-- recent items -->
<div class="flex items-end justify-between mb-5">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Recent Reports</h2>
        <p class="text-sm text-slate-500 mt-1">Latest open items posted by the community</p>
    </div>
    <a href="search.php" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">View all &rarr;</a>
</div>

<?php if ($result->num_rows > 0) { ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php while ($row = $result->fetch_assoc()) { // loop through each item ?>
    <a href="item.php?id=<?php echo $row['item_id']; ?>" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition">
        <div class="flex justify-between items-start mb-3">
            <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase <?php echo $row['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                <?php echo $row['type']; ?>
            </span>
            <span class="text-xs text-slate-500"><?php echo $row['category_name']; ?></span>
        </div>
        <h3 class="font-semibold text-slate-900 group-hover:text-indigo-700 mb-2"><?php echo $row['title']; ?></h3>
        <p class="text-sm text-slate-600 leading-relaxed mb-4"><?php echo substr($row['description'], 0, 100); ?>...</p>
        <div class="flex items-center gap-3 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0L6.343 16.657a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <?php echo $row['location_name']; ?>
            </span>
            <span>&middot;</span>
            <span><?php echo $row['date_occurred']; ?></span>
        </div>
    </a>
    <?php } ?>
</div>
<?php } else { ?>
<div class="bg-white border border-dashed border-slate-300 rounded-xl p-12 text-center">
    <p class="text-slate-500">No items reported yet.</p>
</div>
<?php } ?>

<?php include 'includes/footer.php'; ?>
