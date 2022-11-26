<?php
if(!isset($_SESSION)) { session_start(); }
$_SESSION["LoggedIn"] = false;
$_SESSION["UserID"] = -1;
$_SESSION["Username"] = "";
$_SESSION["Queue"] = null;
$_SESSION["SongIndex"] = 0;
header("location: ../index.php");
?>
