<?php
if(!isset($_SESSION)) { session_start(); }
$_SESSION["LoggedIn"] = false;
$_SESSION["UserID"] = -1;
$_SESSION["Username"] = "";
header("location: ../index.php");
?>
