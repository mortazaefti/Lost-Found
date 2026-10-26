<?php
// start session if not already started
if (!isset($_SESSION)) session_start();
$base = isset($base) ? $base : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost & Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>dist/app.css">
    <style>body{font-family:'Inter',system-ui,sans-serif;}</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">

<!-- top navbar -->
<nav class="bg-white border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="<?php echo $base; ?>index.php" class="uppercase text-slate-900 font-bold text-lg">
            
            Lost <span class="text-indigo-600">&</span> Found
        </a>
        <div class="flex gap-1 text-sm items-center">
            <a href="<?php echo $base; ?>index.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Home</a>
            <a href="<?php echo $base; ?>search.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Search</a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="<?php echo $base; ?>dashboard.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Dashboard</a>
                <a href="<?php echo $base; ?>report_lost.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Report Lost</a>
                <a href="<?php echo $base; ?>report_found.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Report Found</a>
                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <a href="<?php echo $base; ?>admin/dashboard.php" class="px-3 py-2 rounded-md bg-amber-100 text-amber-800 font-semibold hover:bg-amber-200">Admin</a>
                <?php } ?>
                <span class="text-slate-400 mx-2">|</span>
                <a href="<?php echo $base; ?>profile.php" class="flex items-center gap-2 px-2 py-1.5 rounded-md hover:bg-slate-100">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white text-[10px] font-bold flex items-center justify-center"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                    <span class="text-sm text-slate-700"><?php echo $_SESSION['name']; ?></span>
                </a>
                <a href="<?php echo $base; ?>logout.php" class="ml-1 px-3 py-2 rounded-md text-slate-600 hover:text-rose-600 hover:bg-rose-50">Logout</a>
            <?php } else { ?>
                <span class="text-slate-400 mx-2">|</span>
                <a href="<?php echo $base; ?>login.php" class="px-3 py-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">Login</a>
                <a href="<?php echo $base; ?>register.php" class="ml-1 px-4 py-2 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm">Register</a>
            <?php } ?>
        </div>
    </div>
</nav>

<!-- main content -->
<main class="flex-1">
<div class="max-w-6xl mx-auto px-4 py-8">
