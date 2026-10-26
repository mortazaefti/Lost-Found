<?php
session_start();
include '../config/db.php';

// only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// handle all the CRUD actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $conn->real_escape_string($_POST['name']);
        if ($name != "") $conn->query("INSERT INTO categories (name) VALUES ('$name')");
    }
    if (isset($_POST['update_category'])) {
        $id = (int)$_POST['category_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $conn->query("UPDATE categories SET name = '$name' WHERE category_id = $id");
    }
    if (isset($_POST['delete_category'])) {
        $id = (int)$_POST['category_id'];
        $check = $conn->query("SELECT COUNT(*) AS c FROM items WHERE category_id = $id")->fetch_assoc()['c'];
        if ($check == 0) $conn->query("DELETE FROM categories WHERE category_id = $id");
    }
    if (isset($_POST['add_location'])) {
        $name = $conn->real_escape_string($_POST['name']);
        if ($name != "") $conn->query("INSERT INTO locations (name) VALUES ('$name')");
    }
    if (isset($_POST['update_location'])) {
        $id = (int)$_POST['location_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $conn->query("UPDATE locations SET name = '$name' WHERE location_id = $id");
    }
    if (isset($_POST['delete_location'])) {
        $id = (int)$_POST['location_id'];
        $check = $conn->query("SELECT COUNT(*) AS c FROM items WHERE location_id = $id")->fetch_assoc()['c'];
        if ($check == 0) $conn->query("DELETE FROM locations WHERE location_id = $id");
    }
    header("Location: categories.php");
    exit;
}

// load both lists with usage count
$categories = $conn->query("SELECT c.*, COUNT(i.item_id) AS usage_count
                            FROM categories c LEFT JOIN items i ON c.category_id = i.category_id
                            GROUP BY c.category_id ORDER BY c.name");
$locations = $conn->query("SELECT l.*, COUNT(i.item_id) AS usage_count
                           FROM locations l LEFT JOIN items i ON l.location_id = i.location_id
                           GROUP BY l.location_id ORDER BY l.name");

$base = '../';
include '../includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Categories & Locations</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage reference data for items</p>
    </div>
    <a href="dashboard.php" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">&larr; Admin Home</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- categories box -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900">Categories</h2>
        </div>

        <div class="p-5 bg-slate-50 border-b border-slate-200">
            <form method="POST" class="flex gap-2">
                <input type="text" name="name" placeholder="New category name" required class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button name="add_category" value="1" class="bg-indigo-600 text-white px-4 rounded-lg text-sm font-medium hover:bg-indigo-700">+ Add</button>
            </form>
        </div>

        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
            <?php while ($c = $categories->fetch_assoc()) { ?>
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-2.5">
                    <form method="POST" class="flex gap-1">
                        <input type="hidden" name="category_id" value="<?php echo $c['category_id']; ?>">
                        <input type="text" name="name" value="<?php echo $c['name']; ?>" class="border border-slate-300 rounded-md px-2.5 py-1.5 text-sm w-full focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <button name="update_category" value="1" class="bg-slate-600 text-white px-2.5 py-1 rounded-md text-xs font-medium hover:bg-slate-700">Save</button>
                    </form>
                </td>
                <td class="px-3 py-2.5 text-right w-28">
                    <span class="text-xs text-slate-500"><?php echo $c['usage_count']; ?> used</span>
                </td>
                <td class="px-5 py-2.5 text-right w-20">
                    <form method="POST" onsubmit="return confirm('Delete?')" class="inline">
                        <input type="hidden" name="category_id" value="<?php echo $c['category_id']; ?>">
                        <button name="delete_category" value="1" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- locations box -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0L6.343 16.657a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h2 class="font-semibold text-slate-900">Locations</h2>
        </div>

        <div class="p-5 bg-slate-50 border-b border-slate-200">
            <form method="POST" class="flex gap-2">
                <input type="text" name="name" placeholder="New location name" required class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button name="add_location" value="1" class="bg-indigo-600 text-white px-4 rounded-lg text-sm font-medium hover:bg-indigo-700">+ Add</button>
            </form>
        </div>

        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100">
            <?php while ($l = $locations->fetch_assoc()) { ?>
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-2.5">
                    <form method="POST" class="flex gap-1">
                        <input type="hidden" name="location_id" value="<?php echo $l['location_id']; ?>">
                        <input type="text" name="name" value="<?php echo $l['name']; ?>" class="border border-slate-300 rounded-md px-2.5 py-1.5 text-sm w-full focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <button name="update_location" value="1" class="bg-slate-600 text-white px-2.5 py-1 rounded-md text-xs font-medium hover:bg-slate-700">Save</button>
                    </form>
                </td>
                <td class="px-3 py-2.5 text-right w-28">
                    <span class="text-xs text-slate-500"><?php echo $l['usage_count']; ?> used</span>
                </td>
                <td class="px-5 py-2.5 text-right w-20">
                    <form method="POST" onsubmit="return confirm('Delete?')" class="inline">
                        <input type="hidden" name="location_id" value="<?php echo $l['location_id']; ?>">
                        <button name="delete_location" value="1" class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
