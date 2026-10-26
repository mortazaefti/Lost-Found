<?php
session_start();
include 'config/db.php';

// already logged in? go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];

    // check if email already exists
    $check = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        // hash password and save user
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, phone, role)
                VALUES ('$name', '$email', '$hashed', '$phone', 'user')";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

include 'includes/header.php';
?>

<div class="max-w-md mx-auto mt-8">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Create account</h1>
        <p class="text-slate-500 mt-2">Join the campus Lost & Found community</p>
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
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input type="email" name="email" required placeholder="you@email.com" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone <span class="text-slate-400 font-normal">(optional)</span></label>
                <input type="text" name="phone" placeholder="01xxxxxxxxx" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="At least 6 characters" class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-semibold hover:bg-indigo-700 shadow-sm">Create Account</button>
        </form>

        <p class="mt-6 text-sm text-center text-slate-600">
            Already have an account? <a href="login.php" class="text-indigo-600 font-medium hover:text-indigo-700">Sign in</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
