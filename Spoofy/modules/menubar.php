<?php

if(!isset($_SESSION)) { session_start(); }
echo '<div class="topnav">';
echo '<a href="/index.php">Home</a>';
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
if ($isPremium) { echo '<a href="/music/songs.php">Songs</a>'; }
else { echo '<a href="/music/advertisements.php">Advertisements</a>'; }
echo '<a href="/music/search.php">Search</a>';

if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) { 
    echo "<td><a href='/user/profile.php?UserID=" . $_SESSION['UserID'] . "'>Profile</a></td>";
    echo '<a href="/user/logout.php">Logout</a>';
} else {
    echo '<a href="/user/login.php">Login</a>';
    echo '<a href="/user/register.php">Register</a>';
}

echo '</div>';

// Admin Menubar
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    echo '
    <div class="topnav">
        <a><strong>Admin:</strong></a>
        <a href="/admin/manage_users.php">Manage Users</a>
        <a href="/admin/manage_songs.php">Manage Music</a>
        <a href="/admin/manage_ads.php">Manage Advertisements</a>
    </div>';
}

// Song Queue
if (isset($_SESSION["Queue"]) && $_SESSION["Queue"] != null) {
    // Handle button presses for next, prev song
    if (array_key_exists("NextSong", $_POST)) {
        $i = $_SESSION["SongIndex"] + 1;
        if ($i >= count($_SESSION["Queue"])) { $i = 0; }
        $_SESSION["SongIndex"] = $i;
    } else if (array_key_exists("PrevSong", $_POST)) {
        $i = $_SESSION["SongIndex"] - 1;
        if ($i < 0) { $i = count($_SESSION["Queue"]) - 1; }
        $_SESSION["SongIndex"] = $i;
    } else if (array_key_exists("Shuffle", $_POST)) {
        shuffle($_SESSION["Queue"]);
        $_SESSION["SongIndex"] = 0;
    }

    if (array_key_exists("ClearQueue", $_POST)) {
        $_SESSION["Queue"] = null;
        $_SESSION["SongIndex"] = 0;
    } else {
        // Make sure the song index is valid
        if ($_SESSION["SongIndex"] >= 0 && $_SESSION["SongIndex"] < count($_SESSION["Queue"])) {

            // Display current song information
            $SID = $_SESSION["Queue"][$_SESSION["SongIndex"]];
            include "mysql_connect.php";

            // Fetch current song details
            if ($isPremium) {
                $prepare = mysqli_prepare($con, "SELECT Title FROM SONG WHERE SongID=?");
            } else {
                $prepare = mysqli_prepare($con, "SELECT Company FROM ADVERTISEMENT WHERE AdID=?");
            }
            $prepare -> bind_param("s", $SID);
            $prepare -> execute();
            $result = $prepare -> get_result();
            $row = mysqli_fetch_array($result);

            $title = $isPremium ? $row["Title"] : $row["Company"];
            echo '
            <div class="topnav">
                <a><strong>Current Song:</strong></a>';
            if ($isPremium) {
                echo '<a href="/music/song.php?SongID='.$SID.'">'.$title.'</a>';
            } else {
                echo '<a href="/music/advertisement.php?AdID='.$SID.'">'.$title.'</a>';
            }
            echo '<a>('.($_SESSION["SongIndex"] + 1).'/'.count($_SESSION["Queue"]).')</a>
                <a href="/music/queue.php">Queue</a>
            </div>';
            echo "
            <form method=\"post\">
                <input type=\"submit\" name=\"PrevSong\" class=\"button\" value=\"Previous\" />
                <input type=\"submit\" name=\"NextSong\" class=\"button\" value=\"Next\" />
                <input type=\"submit\" name=\"ClearQueue\" class=\"button\" value=\"Clear Queue\" />
                <input type=\"submit\" name=\"Shuffle\" class=\"button\" value=\"Shuffle Queue\" />
            </form>
            ";
        }
    }
}
?>
