<?php
include "modules/menubar.php";
include "modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) { 
    echo "<h1>Welcome ".$_SESSION["Username"]."!</h1>";
} else {
    echo "<h1>Welcome to Spoofy!</h1>";
    echo "<a href=\"/user/login.php\">Log In</a>
            <a href=\"/user/register.php\">Register</a>";
}

// Display songs
$result = mysqli_query($con, "SELECT * FROM Song");
echo "<h3>Your gateway to ".(string)mysqli_num_rows($result)." songs!</h3";
?>

<html>
    <head>
        <title>CPSC 471 - Spoofy</title>
    </head>
</html>
