<?php
// @todo THESE BUTTONS CURRENTLY DON't WORK IF YOU ARENT IN THE ROOT DIRECTORY
echo '
<div class="topnav">
    <a href="/index.php">Home</a>
    <a href="/songs.php">Songs</a>
';

session_start();
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) { 
    echo '<a href="/user/logout.php">Logout</a>';
}

echo '</div>';
?>
