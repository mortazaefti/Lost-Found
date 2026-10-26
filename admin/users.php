<?php
session_start();
include '../config/db.php';

// only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // delete user (but not yourself)
    if (isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        if ($id != $_SESSION['user_id']) {
            $conn->query("DELETE FROM users WHERE user_id = $id");
        }
    }
    // change user role
    if (isset($_POST['update_role'])) {
        $id = (int)$_POST['user_id'];
        $role = $conn->real_escape_string($_POST['role']);
        if ($id != $_SESSION['user_id']) {
            $conn->query("UPDATE users SET role = '$role' WHERE user_id = $id");
        }
    }
    header("Location: users.php");
    exit;
}

// get all users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

$base = '../';
include '../includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Manage Users</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?php echo $result->num_rows; ?> registered users</p>
    </div>
    <a href="dashboard.php" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">&larr; Admin Home</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-slate-600 text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 text-left font-semibold">#</th>
                    <th class="px-4 py-3 text-left font-semibold">User</th>
                    <th class="px-4 py-3 text-left font-semibold">Email</th>
                    <th class="px-4 py-3 text-left font-semibold">Phone</th>
                    <th class="px-4 py-3 text-left font-semibold">Role</th>
                    <th class="px-4 py-3 text-left font-semibold">Joined</th>
                    <th class="px-4 py-3 text-right font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-500 text-xs">#<?php echo $row['user_id']; ?></td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="font-medium text-slate-900"><?php echo $row['name']; ?><?php if ($row['user_id'] == $_SESSION['user_id']) echo ' <span class="text-xs text-slate-400 font-normal">(you)</span>'; ?></div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-slate-600"><?php echo $row['email']; ?></td>
                <td class="px-4 py-3 text-slate-600"><?php echo $row['phone'] ? $row['phone'] : '<span class="text-slate-400">—</span>'; ?></td>
                <td class="px-4 py-3">
                    <?php if ($row['user_id'] != $_SESSION['user_id']) { ?>
                    <form method="POST" class="flex gap-1">
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        <select name="role" class="border border-slate-300 rounded-md px-2 py-1 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                            <option value="user" <?php if ($row['role']=='user') echo 'selected'; ?>>user</option>
                            <option value="admin" <?php if ($row['role']=='admin') echo 'selected'; ?>>admin</option>
                        </select>
                        <button name="update_role" value="1" class="bg-indigo-600 text-white px-2.5 py-1 rounded-md text-xs font-medium hover:bg-indigo-700">Save</button>
                    </form>
                    <?php } else { ?>
                        <span class="text-[10px] tracking-wider px-2 py-1 rounded-full font-bold uppercase bg-amber-100 text-amber-700"><?php echo $row['role']; ?></span>
                    <?php } ?>
                </td>
                <td class="px-4 py-3 text-slate-500 text-xs"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                <td class="px-4 py-3 text-right">
                    <?php if ($row['user_id'] != $_SESSION['user_id']) { ?>
                    <form method="POST" onsubmit="return confirm('Delete user?')" class="inline">
                        <input type="hidden" name="delete_id" value="<?php echo $row['user_id']; ?>">
                        <button class="text-rose-600 hover:text-rose-700 text-xs font-medium">Delete</button>
                    </form>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
