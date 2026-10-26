<?php
session_start();
include '../config/db.php';

// only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int)$_GET['id'];

// get the item to edit
$result = $conn->query("SELECT * FROM items WHERE item_id = $id");
if ($result->num_rows == 0) {
    header("Location: items.php");
    exit;
}
$item = $result->fetch_assoc();

// load dropdowns
$categories = $conn->query("SELECT * FROM categories");
$locations = $conn->query("SELECT * FROM locations");

$error = "";

// update on submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $location_id = (int)$_POST['location_id'];
    $date = $conn->real_escape_string($_POST['date_occurred']);
    $type = $conn->real_escape_string($_POST['type']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "UPDATE items
            SET title='$title', description='$description', category_id=$category_id,
                location_id=$location_id, date_occurred='$date', type='$type', status='$status'
            WHERE item_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: items.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}

$base = '../';
include '../includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <a href="items.php" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Items
    </a>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Edit Item <span class="text-slate-400 font-normal">#<?php echo $id; ?></span></h1>
        <p class="text-sm text-slate-500 mt-0.5">Update the details of this item</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <?php if ($error != "") { ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm p-3 rounded-lg mb-5"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Title</label>
                <input type="text" name="title" value="<?php echo $item['title']; ?>" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea name="description" rows="4" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?php echo $item['description']; ?></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Type</label>
                    <select name="type" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="lost" <?php if ($item['type']=='lost') echo 'selected'; ?>>Lost</option>
                        <option value="found" <?php if ($item['type']=='found') echo 'selected'; ?>>Found</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                    <select name="status" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="open" <?php if ($item['status']=='open') echo 'selected'; ?>>Open</option>
                        <option value="returned" <?php if ($item['status']=='returned') echo 'selected'; ?>>Returned</option>
                        <option value="closed" <?php if ($item['status']=='closed') echo 'selected'; ?>>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <select name="category_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php while ($c = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $c['category_id']; ?>" <?php if ($item['category_id'] == $c['category_id']) echo 'selected'; ?>><?php echo $c['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                    <select name="location_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <?php while ($l = $locations->fetch_assoc()) { ?>
                            <option value="<?php echo $l['location_id']; ?>" <?php if ($item['location_id'] == $l['location_id']) echo 'selected'; ?>><?php echo $l['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
                <input type="date" name="date_occurred" value="<?php echo $item['date_occurred']; ?>" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700 shadow-sm">Update Item</button>
                <a href="items.php" class="px-5 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 font-medium">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
