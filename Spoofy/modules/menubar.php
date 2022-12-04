<?php
include_once "mysql_connect.php";
include_once "queue_functions.php";

function get_song($con, $songID) {
    $sql = "SELECT MusicFile FROM SONG WHERE SongID=?";
    $prepare = mysqli_prepare($con, $sql);
    $prepare -> bind_param("s", $songID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) == 0 || !file_exists('../resources/'.$row["MusicFile"])) {
        return false;
    } else {
        $path = $row["MusicFile"];
    }
    $prepare -> close();
    return $path;
}

if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
$loggedIn = isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"];

echo '<ul class="topnav ul_menubar">';
echo '<li class="li_menubar"><img src="/assets/spoofySmall.png" width="45"><a href="/index.php"></a></li>&nbsp';
echo '<li class="li_menubar"><a href="/index.php">Home</a></li>&nbsp';
if ($isPremium || !$loggedIn) { echo '<li class="li_menubar"><a href="/music/songs.php">Songs</a></li>&nbsp'; }
else { echo '<li class="li_menubar"><a href="/music/advertisements.php">Advertisements</a></li>&nbsp'; }
echo '<li class="li_menubar"><a href="/music/search.php">Search</a></li>&nbsp';

if ($loggedIn) { 
    echo '<li class="li_menubar"><td><a href="/user/profile.php?UserID=' . $_SESSION['UserID'] . '">Profile</a></li>&nbsp</td>';
    echo '<li class="li_menubar" style="float:right"><a href="/user/logout.php">Logout</a></li>&nbsp';
} else {
    echo '<li class="li_menubar"><a href="/user/login.php">Login</a></li>&nbsp';
    echo '<li class="li_menubar"><a href="/user/register.php">Register</a></li>&nbsp';
}

// Admin Menubar
if ($loggedIn && $_SESSION["Admin"]) {
    echo '
        <li class="li_menubar"><a href="/admin/manage_users.php">Manage Users</a></li>&nbsp
        <li class="li_menubar"><a href="/admin/manage_songs.php">Manage Music</a></li>&nbsp
        <li class="li_menubar"><a href="/admin/manage_ads.php">Manage Advertisements</a></li>&nbsp';
}
echo '</ul>';

// Song Queue
if (isset($_SESSION["Queue"]) && $_SESSION["Queue"] != null) {
    // Handle button presses for next, prev song
    if (array_key_exists("NextSong", $_POST)) {
        $i = $_SESSION["SongIndex"] + 1;
        if ($i >= count($_SESSION["Queue"])) { $i = 0; }
        $_SESSION["SongIndex"] = $i;
        increment_song_plays($con, $_SESSION["Queue"][$i]);
    } else if (array_key_exists("PrevSong", $_POST)) {
        $i = $_SESSION["SongIndex"] - 1;
        if ($i < 0) { $i = count($_SESSION["Queue"]) - 1; }
        $_SESSION["SongIndex"] = $i;
        increment_song_plays($con, $_SESSION["Queue"][$i]);
    } else if (array_key_exists("Shuffle", $_POST)) {
        shuffle($_SESSION["Queue"]);
        $_SESSION["SongIndex"] = 0;
        increment_song_plays($con, $_SESSION["Queue"][0]);
    }

    if (array_key_exists("ClearQueue", $_POST)) {
        $_SESSION["Queue"] = null;
        $_SESSION["SongIndex"] = 0;
    } else {
        // Make sure the song index is valid
        if ($_SESSION["SongIndex"] >= 0 && $_SESSION["SongIndex"] < count($_SESSION["Queue"])) {

            // Display current song information
            $SID = $_SESSION["Queue"][$_SESSION["SongIndex"]];

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
                <a href="/music/queue.php">Queue</a>';

            if ($isPremium) {
                $path = get_song($con, $SID);
                if ($path != false) {
                    echo '<audio controls>
                        <source src="/resources/'.$path.'" type="audio/mpeg">
                    </audio>';
                }
            }
            
            echo '</div>';
            echo "
            <form method=\"post\">
                <input type=\"submit\" name=\"PrevSong\" class=\"button\" value=\"Previous\" />
                <input type=\"submit\" name=\"NextSong\" class=\"button\" value=\"Next\" />
                <input type=\"submit\" name=\"ClearQueue\" class=\"button\" value=\"Clear Queue\" />
                <input type=\"submit\" name=\"Shuffle\" class=\"button\" value=\"Shuffle Queue\" />
            </form>
            ";
            $prepare -> close();
        }
    }
}
?>
