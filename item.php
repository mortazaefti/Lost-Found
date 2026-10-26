<?php
session_start();
include 'config/db.php';

$id = (int)$_GET['id'];

// get full item details with reporter info
$sql = "SELECT items.*, categories.name AS category_name, locations.name AS location_name,
               users.name AS reporter_name, users.email AS reporter_email, users.phone AS reporter_phone
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN locations ON items.location_id = locations.location_id
        JOIN users ON items.user_id = users.user_id
        WHERE items.item_id = $id";
$result = $conn->query($sql);

// not found? go home
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$item = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="max-w-3xl mx-auto">
    <a href="search.php" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Search
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
       
        <div class="p-8">
            <div class="flex gap-2 mb-4">
                <span class="text-[10px] tracking-wider px-2.5 py-1 rounded-full font-bold uppercase <?php echo $item['type']=='lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'; ?>"><?php echo $item['type']; ?></span>
                <span class="text-[10px] tracking-wider px-2.5 py-1 rounded-full font-bold uppercase bg-slate-100 text-slate-600"><?php echo $item['status']; ?></span>
            </div>

            <h1 class="text-3xl font-bold text-slate-900 mb-4"><?php echo $item['title']; ?></h1>
            <p class="text-slate-700 leading-relaxed mb-8 whitespace-pre-wrap"><?php echo $item['description']; ?></p>

            <!-- item details grid -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider">Category</div>
                        <div class="font-medium text-slate-900 mt-0.5"><?php echo $item['category_name']; ?></div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0L6.343 16.657a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider">Location</div>
                        <div class="font-medium text-slate-900 mt-0.5"><?php echo $item['location_name']; ?></div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider">Date</div>
                        <div class="font-medium text-slate-900 mt-0.5"><?php echo $item['date_occurred']; ?></div>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-lg">
                    <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500 uppercase tracking-wider">Reported By</div>
                        <div class="font-medium text-slate-900 mt-0.5"><?php echo $item['reporter_name']; ?></div>
                    </div>
                </div>
            </div>

            <!-- contact section -->
            <?php if (isset($_SESSION['user_id'])) { ?>
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Contact Information</h3>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 space-y-2">
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-slate-500 w-16">Email</span>
                        <a href="mailto:<?php echo $item['reporter_email']; ?>" class="text-indigo-700 font-medium hover:underline"><?php echo $item['reporter_email']; ?></a>
                    </div>
                    <?php if ($item['reporter_phone']) { ?>
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-slate-500 w-16">Phone</span>
                        <a href="tel:<?php echo $item['reporter_phone']; ?>" class="text-indigo-700 font-medium hover:underline"><?php echo $item['reporter_phone']; ?></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } else { ?>
            <div class="border-t border-slate-200 pt-6">
                <div class="bg-slate-50 rounded-lg p-6 text-center">
                    <p class="text-slate-600 mb-3 text-sm">Please sign in to see contact information</p>
                    <a href="login.php" class="inline-block bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg font-medium hover:bg-indigo-700">Sign In</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
