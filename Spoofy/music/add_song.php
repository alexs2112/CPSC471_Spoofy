<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";
include "../modules/playlist_functions.php";

if (!array_key_exists("SongID", $_GET)) { header("location: /index.php"); }
$SongID = $_GET["SongID"];

if (!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
    $UserID = $_SESSION["UserID"];

    // Buttons to add this song to a playlist after validating user
    if($_SERVER["REQUEST_METHOD"] == "POST") {
            
        // Add song to playlist that is owned by the user
        $prepare = mysqli_prepare($con, "SELECT PlaylistID FROM PLAYLIST WHERE CreatorID=?");
        $prepare -> bind_param("s", $UserID);
        $prepare -> execute();
        $result = $prepare -> get_result();
        while ($row = mysqli_fetch_array($result)) {
            if (array_key_exists("add_to_".$row["PlaylistID"], $_POST)) {
                add_song($con, $row["PlaylistID"], $SongID);
                header("location: /music/playlist.php?PlaylistID=".$row["PlaylistID"]);
            }
        }
    }

    // Write the name of the song in the header
    $prepare = mysqli_prepare($con, "SELECT Title FROM SONG WHERE SongID=?");
    $prepare -> bind_param("s", $SongID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $row = mysqli_fetch_array($result);
    $SongTitle = $row["Title"];
    echo "<h3>Adding ".$SongTitle." to a playlist.</h3>";

    // List off the playlists this user owns
    // Below code nearly fully copied from profile.php
    $prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST WHERE CreatorID=?");
    $prepare -> bind_param("s", $UserID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    if (mysqli_num_rows($result) < 1) {
        echo "<h3>No Playlists.</h3>";
        echo "<p>Playlists can be created from the <a href='/user/profile.php?UserID=".$UserID."'>Profile Page</a>.</p>";
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
                <td><a href='/music/playlist.php?PlaylistID= " . $playlist["PlaylistID"] . "'>View</a></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"add_to_" . $playlist["PlaylistID"] . "\" class=\"button\" value=\"Select\" />
                </form></td>
                </tr>";
        }
        echo "</table>";
    }
} else {
    header("location: /user/login.php");
}
?>

<html>
    <head>
        <title>Adding <?php echo $SongTitle; ?> - Spoofy</title>
    </head>
</html>
