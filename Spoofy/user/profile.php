<?php
include "../modules/mysql_connect.php";
include "../modules/playlist_functions.php";

$ID = $_GET["UserID"];

// POST is called here to handle the playlists under this user
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!isset($_SESSION)) { session_start(); }
    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["UserID"] == $ID) {
        
        // Create playlist
        if (array_key_exists("create_playlist_name", $_POST)) {
            $playlist_name = trim($_POST["create_playlist_name"]);
            create_playlist($con, $playlist_name, $ID);
        }

        // Delete playlist by ID
        // Make sure the playlist is owned by this user
        $prepare = mysqli_prepare($con, "SELECT PlaylistID FROM PLAYLIST WHERE CreatorID=?");
        $prepare -> bind_param("s", $ID);
        $prepare -> execute();
        $result = $prepare -> get_result();
        while ($row = mysqli_fetch_array($result)) {
            if (array_key_exists("clear_playlist_".$row["PlaylistID"], $_POST)) {
                delete_playlist($con, $row["PlaylistID"]);
            } else if (array_key_exists("play_playlist_".$row["PlaylistID"], $_POST)) {
                play_playlist($con, $row["PlaylistID"]);
            }
        }
    }
}

// Playing the playlist will modify the menubar
include "../modules/menubar.php";

if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
$isUser = isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["UserID"] == $ID;

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

// Display playlists owned by this user if the current user is free
if ($isPremium) {
    $prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST WHERE CreatorID=?");
    $prepare -> bind_param("s", $ID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    if (mysqli_num_rows($result) < 1) {
        echo "<h3>No Playlists.</h3>";
    } else {
        echo "<h3>Playlists:</h3>";
        echo "<table border='1'>
            <tr>
            <th>Playlist</th>
            <th>Songs</th>
            </tr>";
        while ($playlist = mysqli_fetch_array($result)) {
            // Get count of songs
            $prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST_CONTAINS WHERE PlaylistID=?");
            $prepare -> bind_param("s", $playlist["PlaylistID"]);
            $prepare -> execute();
            $songs = $prepare -> get_result();
            $song_count = mysqli_num_rows($songs);

            echo "<tr>
                <td>" . $playlist["PlaylistName"] . "</td>
                <td>" . $song_count . "</td>
                <td><a href='/music/playlist.php?PlaylistID=" . $playlist["PlaylistID"] . "'>View</a></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"clear_playlist_" . $playlist["PlaylistID"] . "\"
                        onclick=\"return confirm('Are you sure you would like to delete " . $playlist["PlaylistName"] . "?');\"
                        class=\"button\" value=\"Delete\" />
                </form></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"play_playlist_" . $playlist["PlaylistID"] . "\"
                        class=\"button\" value=\"Play Playlist\" />
                </form></td>
                </tr>";
        }
        echo "</table>";
    }
} else {
    if ($isUser) {
        echo "<h3>Your Free Account Cannot Create Playlists.</h3>";
    } else {
        echo "<h3>Your Free Account Cannot See Playlists.</h3>";
    }
}

// Options that only show up if the profile owner is viewing the page
if ($isUser) { 
    // Allow premium users to create a new playlist
    if ($isPremium) {
        echo "
        <form action=profile.php?UserID=".$ID." method=\"post\">
            <div class=\"form-group\">
                <label>New Playlist</label>
                <input type=\"text\" name=\"create_playlist_name\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <input type=\"submit\" class=\"btn btn-primary\" value=\"Create\">
            </div>
        </form>";
    }

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
