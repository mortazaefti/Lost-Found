<?php
session_start();
include 'config/db.php';

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get dropdown data
$categories = $conn->query("SELECT * FROM categories");
$locations = $conn->query("SELECT * FROM locations");

$error = "";

// form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $location_id = (int)$_POST['location_id'];
    $date = $conn->real_escape_string($_POST['date_occurred']);
    $user_id = $_SESSION['user_id'];

    // insert lost item
    $sql = "INSERT INTO items (user_id, category_id, location_id, type, title, description, date_occurred)
            VALUES ($user_id, $category_id, $location_id, 'lost', '$title', '$description', '$date')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Report a Lost Item</h1>
        <p class="text-slate-500 mt-1">Fill out the details so others can help find it</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <?php if ($error != "") { ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm p-3 rounded-lg mb-5"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Item Title</label>
                <input type="text" name="title" required placeholder="e.g. Black leather wallet" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                <textarea name="description" rows="4" required placeholder="Describe the item in detail - colour, brand, any marks, contents..." class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
                    <select name="category_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select category</option>
                        <?php while ($c = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $c['category_id']; ?>"><?php echo $c['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Seen Location</label>
                    <select name="location_id" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select location</option>
                        <?php while ($l = $locations->fetch_assoc()) { ?>
                            <option value="<?php echo $l['location_id']; ?>"><?php echo $l['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Date Lost</label>
                <input type="date" name="date_occurred" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-rose-600 text-white py-2.5 rounded-lg font-semibold hover:bg-rose-700 shadow-sm">Submit Report</button>
                <a href="dashboard.php" class="px-5 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 font-medium">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
