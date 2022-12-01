<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";
include "../modules/playlist_functions.php";

$PlaylistID = $_GET["PlaylistID"];

// Button POST requests for removing songs, playing songs, playing playlist
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists("delete_playlist", $_POST)) {
        // Make sure the user is the creator of this playlist before deleting it
        if (!isset($_SESSION)) { session_start(); }
        if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
            $prepare = mysqli_prepare($con, "SELECT CreatorID FROM PLAYLIST WHERE PlaylistID=?");
            $prepare -> bind_param("s", $PlaylistID);
            $prepare -> execute();
            $result = $prepare -> get_result();
            $row = mysqli_fetch_array($result);
            $creatorID = $row["CreatorID"];

            if ($_SESSION["UserID"] == $creatorID) {
                delete_playlist($con, $PlaylistID);
                header("location: /user/profile.php?UserID=".$creatorID);
            }
        }
    }
}

// Get playlist information
$prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST WHERE PlaylistID=?");
$prepare -> bind_param("s", $PlaylistID);
$prepare -> execute();
$result = $prepare -> get_result();
if (mysqli_num_rows($result) < 1) {
    echo "<h3>Could not find playlist.</h3>";
} else {
    $playlist = mysqli_fetch_array($result);
    echo "<h1>".$playlist["PlaylistName"]."</h1>";

    // List off each song in the playlist
    $prepare = mysqli_prepare($con, "SELECT * FROM PLAYLIST_CONTAINS WHERE PlaylistID=?");
    $prepare -> bind_param("s", $PlaylistID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    if (mysqli_num_rows($result) < 1) {
        echo "<p>No songs.</p>";
        echo "<p>Add songs from the <a href='search.php'>search page</a>.</p>";
    } else {
        echo "<table border='1'>
        <tr>
        <th>Title</th>
        <th>Duration</th>
        </tr>";

        // For each song, display their information
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>
            <td>" . $row['Title'] . "</td>
            <td>" . $row['Duration'] . "</td>
            <td><a href='/music/song.php?SongID= " . $row['SongID'] . "'>View</a></td>
            </tr>";
        }
        echo "</table>";
    }

    // Button to delete this playlist
    echo "
    <form method=\"post\">
        <input type=\"submit\" name=\"delete_playlist\"
            onclick=\"return confirm('Are you sure you would like to delete " . $playlist["PlaylistName"] . "?');\"
            class=\"button\" value=\"Delete\" />
    </form>";
}
?>

<html>
    <head>
        <title>Playlist - Spoofy</title>
    </head>
</html>
