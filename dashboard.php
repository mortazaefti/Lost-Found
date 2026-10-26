<?php
session_start();
include 'config/db.php';

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// update item status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE items SET status = '$status' WHERE item_id = $item_id AND user_id = $user_id");
}

// delete item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $conn->query("DELETE FROM items WHERE item_id = $id AND user_id = $user_id");
}

// get my items
$sql = "SELECT items.*, categories.name AS category_name, locations.name AS location_name
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN locations ON items.location_id = locations.location_id
        WHERE items.user_id = $user_id
        ORDER BY items.created_at DESC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 mt-1">Manage your reports and update item status</p>
</div>

<!-- quick action cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="report_lost.php" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-rose-300 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-rose-700">Report Lost</div>
            <div class="text-sm text-slate-500">I've lost something</div>
        </div>
    </a>
    <a href="report_found.php" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-emerald-300 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-emerald-700">Report Found</div>
            <div class="text-sm text-slate-500">I found an item</div>
        </div>
    </a>
    <a href="search.php" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
        </div>
        <div>
            <div class="font-semibold text-slate-900 group-hover:text-indigo-700">Browse Items</div>
            <div class="text-sm text-slate-500">Search reports</div>
        </div>
    </a>
</div>

<!-- my items table -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <div>
            <h2 class="font-semibold text-slate-900">My Reported Items</h2>
            <p class="text-xs text-slate-500 mt-0.5"><?php echo $result->num_rows; ?> total</p>
        </div>
    </div>

    <?php if ($result->num_rows > 0) { ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-slate-600 text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 text-left font-semibold">Title</th>
                    <th class="px-6 py-3 text-left font-semibold">Type</th>
                    <th class="px-6 py-3 text-left font-semibold">Category</th>
                    <th class="px-6 py-3 text-left font-semibold">Location</th>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-right font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-3.5">
                    <a href="item.php?id=<?php echo $row['item_id']; ?>" class="font-medium text-slate-900 hover:text-indigo-600"><?php echo $row['title']; ?></a>
                </td>
                <td class="px-6 py-3.5">
                    <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase <?php echo $row['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>"><?php echo $row['type']; ?></span>
                </td>
                <td class="px-6 py-3.5 text-slate-600"><?php echo $row['category_name']; ?></td>
                <td class="px-6 py-3.5 text-slate-600"><?php echo $row['location_name']; ?></td>
                <td class="px-6 py-3.5 text-slate-600"><?php echo $row['date_occurred']; ?></td>
                <td class="px-6 py-3.5">
                    <!-- change status -->
                    <form method="POST" class="flex gap-1">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                        <select name="status" class="border border-slate-300 rounded-md px-2 py-1 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            <option value="open" <?php if ($row['status']=='open') echo 'selected'; ?>>Open</option>
                            <option value="returned" <?php if ($row['status']=='returned') echo 'selected'; ?>>Returned</option>
                            <option value="closed" <?php if ($row['status']=='closed') echo 'selected'; ?>>Closed</option>
                        </select>
                        <button class="bg-indigo-600 text-white px-2.5 py-1 rounded-md text-xs font-medium hover:bg-indigo-700">Save</button>
                    </form>
                </td>
                <td class="px-6 py-3.5 text-right">
                    <!-- delete item -->
                    <form method="POST" onsubmit="return confirm('Delete this item?')" class="inline">
                        <input type="hidden" name="delete_id" value="<?php echo $row['item_id']; ?>">
                        <button class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-slate-500 mb-4">You haven't reported any items yet.</p>
            <a href="report_lost.php" class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg font-medium hover:bg-indigo-700">Report your first item</a>
        </div>
    <?php } ?>
</div>

<?php include 'includes/footer.php'; ?>
