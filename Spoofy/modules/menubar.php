<?php
echo '
<div class="topnav">
    <a href="/index.php">Home</a>
    <a href="/songs.php">Songs</a>
';

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) { 
    echo "<td><a href='/user/profile.php?UserID= " . $_SESSION['UserID'] . "'>Profile</a></td>";
    echo '<a href="/user/logout.php">Logout</a>';
} else {
    echo '<a href="/user/login.php">Login</a>';
    echo '<a href="/user/register.php">Register</a>';
}

echo '</div>';

if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    echo '
    <div class="topnav">
        <a><strong>Admin:</strong></a>
        <a href="/admin/manage_users.php">Manage Users</a>
        <a href="/admin/manage_music.php">Manage Music</a>
        <a href="/admin/manage_ads.php">Manage Advertisements</a>
    </div>';
}
?>
