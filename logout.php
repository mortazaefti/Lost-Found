<?php
// end session and go home
session_start();
session_destroy();
header("Location: index.php");
?>
