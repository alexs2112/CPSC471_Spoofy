<?php
include "../modules/mysql_connect.php";
include "../modules/playlist_functions.php";

$PlaylistID = $_GET["PlaylistID"];
if (!isset($_SESSION)) { session_start(); }

// Button POST requests for removing songs, playing songs, playing playlist
if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";
    if (array_key_exists("delete_playlist", $_POST)) {
        // Make sure the user is the creator of this playlist before deleting it
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
    } else if (array_key_exists("play_playlist", $_POST)) {
        play_playlist($con, $PlaylistID);
    }

    // See if a song is being interacted with
    $prepare = mysqli_prepare($con, "SELECT SongID FROM PLAYLIST_CONTAINS WHERE PlaylistID=?");
    $prepare -> bind_param("s", $PlaylistID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists("remove".$row["SongID"], $_POST)) {
            remove_song($con, $PlaylistID, $row["SongID"]);
        } else if (array_key_exists("play".$row["SongID"], $_POST)) {
            play_song($row["SongID"]);
            increment_song_plays($con, $row["SongID"]);
        } else if (array_key_exists("queue".$row["SongID"], $_POST)) {
            add_song_to_queue($row["SongID"]);
        }
    }
}

// Playing the playlist will modify the menubar
include "../modules/menubar.php";

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
    $prepare = mysqli_prepare($con, "SELECT SongID FROM PLAYLIST_CONTAINS WHERE PlaylistID=?");
    $prepare -> bind_param("s", $PlaylistID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    if (mysqli_num_rows($result) < 1) {
        echo "<p>No songs.</p>";
        echo "<p>Add songs from the <a href='/music/search.php'>search page</a>.</p>";
    } else {
        echo "<table border='1'>
        <tr>
        <th>Title</th>
        <th>Duration</th>
        </tr>";

        // For each song, display their information
        while ($row = mysqli_fetch_array($result)) {
            $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
            $prepare -> bind_param("s", $row["SongID"]);
            $prepare -> execute();
            $song_details = $prepare -> get_result();
            $song = mysqli_fetch_array($song_details);

            echo "<tr>
            <td>" . $song['Title'] . "</td>
            <td>" . $song['Duration'] . "</td>
            <td><a href='/music/song.php?SongID= " . $song['SongID'] . "'>View</a></td>";

            if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["UserID"] == $playlist["CreatorID"]) {
                "<td><form method=\"post\">
                    <input type=\"submit\" name=\"remove" . $song["SongID"] . "\" class=\"button\" value=\"Remove\" />
                </form></td>";
            }

            if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
                "<td><form method=\"post\">
                    <input type=\"submit\" name=\"play" . $song["SongID"] . "\" class=\"button\" value=\"Play\" />
                </form></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"queue" . $song["SongID"] . "\" class=\"button\" value=\"Add to Queue\" />
                </form></td>";
            }

            "</tr>";
        }
        echo "</table>";
    }

    // Buttons to play or delete this playlist
    echo "<form method=\"post\">";

    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["UserID"] == $playlist["CreatorID"]) {
        echo "<input type=\"submit\" name=\"delete_playlist\"
                onclick=\"return confirm('Are you sure you would like to delete " . $playlist["PlaylistName"] . "?');\"
                class=\"button\" value=\"Delete\" />";
    }
    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<input type=\"submit\" name=\"play_playlist\"
                class=\"button\" value=\"Play Playlist\" />";
    }

    echo "</form>";
    $prepare -> close();
    mysqli_close($con);
}
?>

<html>
    <head>
        <link href="/styles/style.css" rel="stylesheet" />
        <title>Playlist - Spoofy</title>
    </head>
</html>
