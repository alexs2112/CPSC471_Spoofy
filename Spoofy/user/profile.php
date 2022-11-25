<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

$ID = $_GET["UserID"];

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM USER WHERE UserID=?");
$prepare -> bind_param("s", $ID);
$prepare -> execute();
$result = $prepare -> get_result();
$row = mysqli_fetch_array($result);

// Display Account Details
// @todo: add Admin to account type
echo "<h1>".$row["Username"]."</h1>";
echo "<p>Account Type: ".($row['IsPremium'] ? "Premium" : "Free")."</p>";

// Options that only show up if the profile owner is viewing the page
if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["UserID"] == $ID) { 
    // Logout button
    echo '<a href="/user/logout.php">Logout</a><p></p>';

    // Upgrade or cancel premium
    if ($row['IsPremium']) {
        echo "<a href=\"/user/update_premium.php?Premium=false\" onclick=\"return confirm('Are you sure you would like to cancel Premium Membership?');\">Cancel Premium Membership</a>";
    } else {
        echo "<a href=\"/user/update_premium.php?Premium=true\">Subscribe for Premium Membership</a>";
    }
}

mysqli_close($con);
?>

<html>
    <head>
        <title>Profile - Spoofy</title>
    </head>
</html>
