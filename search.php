<?php
session_start();
include 'config/db.php';

// load dropdowns
$categories = $conn->query("SELECT * FROM categories");
$locations = $conn->query("SELECT * FROM locations");

// get filter values from URL
$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$location_id = isset($_GET['location_id']) ? (int)$_GET['location_id'] : 0;
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// build query based on filters
$sql = "SELECT items.*, categories.name AS category_name, locations.name AS location_name
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN locations ON items.location_id = locations.location_id
        WHERE 1=1";

// add each filter if present
if ($q != "") $sql .= " AND (items.title LIKE '%$q%' OR items.description LIKE '%$q%')";
if ($type != "") $sql .= " AND items.type = '$type'";
if ($category_id > 0) $sql .= " AND items.category_id = $category_id";
if ($location_id > 0) $sql .= " AND items.location_id = $location_id";
if ($status != "") $sql .= " AND items.status = '$status'";

$sql .= " ORDER BY items.created_at DESC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-900">Search Items</h1>
    <p class="text-slate-500 mt-1">Filter by keyword, type, category, location or status</p>
</div>

<!-- search filters -->
<form method="GET" class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <div class="md:col-span-2 relative">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
            <input type="text" name="q" value="<?php echo $q; ?>" placeholder="Search keyword..." class="w-full border border-slate-300 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <select name="type" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Types</option>
            <option value="lost" <?php if ($type=='lost') echo 'selected'; ?>>Lost</option>
            <option value="found" <?php if ($type=='found') echo 'selected'; ?>>Found</option>
        </select>

        <select name="category_id" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Categories</option>
            <?php while ($c = $categories->fetch_assoc()) { ?>
                <option value="<?php echo $c['category_id']; ?>" <?php if ($category_id == $c['category_id']) echo 'selected'; ?>><?php echo $c['name']; ?></option>
            <?php } ?>
        </select>

        <select name="location_id" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Locations</option>
            <?php while ($l = $locations->fetch_assoc()) { ?>
                <option value="<?php echo $l['location_id']; ?>" <?php if ($location_id == $l['location_id']) echo 'selected'; ?>><?php echo $l['name']; ?></option>
            <?php } ?>
        </select>

        <select name="status" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Statuses</option>
            <option value="open" <?php if ($status=='open') echo 'selected'; ?>>Open</option>
            <option value="returned" <?php if ($status=='returned') echo 'selected'; ?>>Returned</option>
            <option value="closed" <?php if ($status=='closed') echo 'selected'; ?>>Closed</option>
        </select>
    </div>
    <div class="flex gap-2 mt-3">
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700">Apply Filters</button>
        <a href="search.php" class="px-5 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-100 font-medium">Reset</a>
    </div>
</form>

<p class="mb-4 text-sm text-slate-600">Showing <span class="font-semibold text-slate-900"><?php echo $result->num_rows; ?></span> result<?php echo $result->num_rows == 1 ? '' : 's'; ?></p>

<?php if ($result->num_rows > 0) { ?>
<!-- result cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php while ($row = $result->fetch_assoc()) { ?>
    <a href="item.php?id=<?php echo $row['item_id']; ?>" class="group bg-white border border-slate-200 rounded-xl p-5 hover:border-indigo-300 hover:shadow-md transition">
        <div class="flex justify-between items-start mb-3">
            <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase <?php echo $row['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                <?php echo $row['type']; ?>
            </span>
            <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase bg-slate-100 text-slate-600"><?php echo $row['status']; ?></span>
        </div>
        <h3 class="font-semibold text-slate-900 group-hover:text-indigo-700 mb-2"><?php echo $row['title']; ?></h3>
        <p class="text-sm text-slate-600 leading-relaxed mb-4"><?php echo substr($row['description'], 0, 100); ?>...</p>
        <div class="flex items-center gap-3 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0L6.343 16.657a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <?php echo $row['location_name']; ?>
            </span>
            <span>&middot;</span>
            <span><?php echo $row['category_name']; ?></span>
        </div>
    </a>
    <?php } ?>
</div>
<?php } else { ?>
<div class="bg-white border border-dashed border-slate-300 rounded-xl p-12 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
    <p class="text-slate-500">No items match your search.</p>
    <a href="search.php" class="inline-block mt-3 text-sm text-indigo-600 font-medium hover:text-indigo-700">Clear filters</a>
</div>
<?php } ?>

<?php include 'includes/footer.php'; ?>
