<?php
session_start();
include '../config/db.php';

// only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // delete an item
    if (isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        $conn->query("DELETE FROM items WHERE item_id = $id");
    }
    // change status
    if (isset($_POST['update_status'])) {
        $id = (int)$_POST['item_id'];
        $status = $conn->real_escape_string($_POST['status']);
        $conn->query("UPDATE items SET status = '$status' WHERE item_id = $id");
    }
    header("Location: items.php");
    exit;
}

// get all items with category, location, reporter
$sql = "SELECT items.*, categories.name AS category_name, locations.name AS location_name, users.name AS reporter
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN locations ON items.location_id = locations.location_id
        JOIN users ON items.user_id = users.user_id
        ORDER BY items.created_at DESC";
$result = $conn->query($sql);

$base = '../';
include '../includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Manage Items</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?php echo $result->num_rows; ?> total items</p>
    </div>
    <a href="dashboard.php" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">&larr; Admin Home</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-slate-600 text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 text-left font-semibold">#</th>
                    <th class="px-4 py-3 text-left font-semibold">Title</th>
                    <th class="px-4 py-3 text-left font-semibold">Type</th>
                    <th class="px-4 py-3 text-left font-semibold">Category</th>
                    <th class="px-4 py-3 text-left font-semibold">Location</th>
                    <th class="px-4 py-3 text-left font-semibold">Reporter</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500 text-xs">#<?php echo $row['item_id']; ?></td>
                    <td class="px-4 py-3">
                        <a href="../item.php?id=<?php echo $row['item_id']; ?>" class="font-medium text-slate-900 hover:text-indigo-600"><?php echo $row['title']; ?></a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase <?php echo $row['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>"><?php echo $row['type']; ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?php echo $row['category_name']; ?></td>
                    <td class="px-4 py-3 text-slate-600"><?php echo $row['location_name']; ?></td>
                    <td class="px-4 py-3 text-slate-600"><?php echo $row['reporter']; ?></td>
                    <td class="px-4 py-3">
                        <form method="POST" class="flex gap-1">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <select name="status" class="border border-slate-300 rounded-md px-2 py-1 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                <option value="open" <?php if ($row['status']=='open') echo 'selected'; ?>>Open</option>
                                <option value="returned" <?php if ($row['status']=='returned') echo 'selected'; ?>>Returned</option>
                                <option value="closed" <?php if ($row['status']=='closed') echo 'selected'; ?>>Closed</option>
                            </select>
                            <button name="update_status" value="1" class="bg-indigo-600 text-white px-2.5 py-1 rounded-md text-xs font-medium hover:bg-indigo-700">Save</button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex gap-4 justify-end items-center">
                            <a href="edit_item.php?id=<?php echo $row['item_id']; ?>" class="text-amber-600 hover:text-amber-700 text-xs font-medium">Edit</a>
                            <form method="POST" onsubmit="return confirm('Delete?')" class="inline-flex m-0">
                                <input type="hidden" name="delete_id" value="<?php echo $row['item_id']; ?>">
                                <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-500">No items yet.</td></tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
