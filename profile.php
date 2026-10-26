<?php
session_start();
include 'config/db.php';

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";
$error = "";

// handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // check if email is taken by another user
    $check = $conn->query("SELECT user_id FROM users WHERE email = '$email' AND user_id != $user_id");
    if ($check->num_rows > 0) {
        $error = "That email is already used by another account.";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE user_id = $user_id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['name'] = $name;
            $msg = "Profile updated successfully.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // get current hashed password from db
    $result = $conn->query("SELECT password FROM users WHERE user_id = $user_id");
    $user = $result->fetch_assoc();

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } else if (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } else if ($new != $confirm) {
        $error = "New passwords do not match.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed' WHERE user_id = $user_id");
        $msg = "Password changed successfully.";
    }
}

// get current user data
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
        <p class="text-slate-500 mt-1">Update your information and account settings</p>
    </div>

    <?php if ($msg != "") { ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm p-3 rounded-lg mb-5 flex items-start gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo $msg; ?>
        </div>
    <?php } ?>

    <?php if ($error != "") { ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm p-3 rounded-lg mb-5 flex items-start gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <!-- profile info form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 mb-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white text-lg font-bold flex items-center justify-center">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Personal Information</h2>
                <p class="text-xs text-slate-500">Update your name, email, and phone</p>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                <input type="text" name="name" value="<?php echo $user['name']; ?>" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone <span class="text-slate-400 font-normal">(optional)</span></label>
                <input type="text" name="phone" value="<?php echo $user['phone']; ?>" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" name="update_profile" value="1" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 shadow-sm">Save Changes</button>
        </form>
    </div>

    <!-- change password form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Change Password</h2>
                <p class="text-xs text-slate-500">Choose a strong password you haven't used before</p>
            </div>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <input type="password" name="new_password" required placeholder="At least 6 characters" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="confirm_password" required class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" name="change_password" value="1" class="bg-slate-800 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-slate-900 shadow-sm">Change Password</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
