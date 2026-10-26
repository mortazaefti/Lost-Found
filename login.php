<?php
session_start();
include 'config/db.php';

// already logged in? go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // find user by email
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // check password
        if (password_verify($password, $user['password'])) {
            // save session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // admin goes to admin page
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    }
    $error = "Invalid email or password.";
}

include 'includes/header.php';
?>

<div class="max-w-md mx-auto mt-8">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Welcome back</h1>
        <p class="text-slate-500 mt-2">Login to your account to continue</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <?php if ($error != "") { ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm p-3 rounded-lg mb-5 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="you@email.com" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700 shadow-sm">Sign In</button>
        </form>

        <p class="mt-6 text-sm text-center text-slate-600">
            Don't have an account? <a href="register.php" class="text-indigo-600 font-medium hover:text-indigo-700">Sign up</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
