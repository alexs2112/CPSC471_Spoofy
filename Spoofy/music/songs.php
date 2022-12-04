<?php
include "../modules/mysql_connect.php";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";

    // See if a song is being interacted with
    $prepare = mysqli_prepare($con, "SELECT SongID FROM SONG");
    $prepare -> execute();
    $result = $prepare -> get_result();
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists("play".$row["SongID"], $_POST)) {
            play_song($row["SongID"]);
            increment_song_plays($con, $row["SongID"]);
        } else if (array_key_exists("queue".$row["SongID"], $_POST)) {
            add_song_to_queue($row["SongID"]);
        }
    }
}
include "../modules/menubar.php";
include "../modules/image_functions.php";

if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
if (!$isPremium && isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
    header("location: /music/advertisements.php");
}

// Display songs
$prepare = mysqli_prepare($con, "SELECT * FROM SONG");
$prepare -> execute();
$result = $prepare -> get_result();

echo "<h1 class='centered_text'>Song List</h1>";
echo "<table border='1' class='centered_table'>
<tr>
<th></th>
<th>Title</th>
<th>Duration</th>
</tr>";

while($row = mysqli_fetch_array($result)) {
    echo "<tr>
        <td><img id='cover_thumb' src='/resources/" . song_cover($con, $row['SongID']) . "' alt='cover'></td>
        <td>" . $row['Title'] . "</td>
        <td>" . $row['Duration'] . "</td>
        <td><a href='/music/song.php?SongID=" . $row['SongID'] . "'>View</a></td>";

        if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
            echo "<td><a href='/music/add_song.php?SongID=" . $row['SongID'] . "'>Add to Playlist</a></td>
            <td><form method=\"post\">
                <input type=\"submit\" name=\"play" . $row["SongID"] . "\" class=\"playButton\" value=\"Play\" />
            </form></td>
            <td><form method=\"post\">
                <input type=\"submit\" name=\"queue" . $row["SongID"] . "\" class=\"addButton\" value=\"Add to Queue\" />
            </form></td>";
        }
        echo "</tr>";
}
echo "</table>";

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
    <link href="/styles/style.css" rel="stylesheet" />
        <title>Songs - Spoofy</title>
    </head>
</html>
