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
        } else if (array_key_exists("queue".$row["SongID"], $_POST)) {
            add_song_to_queue($row["SongID"]);
        }
    }
}
include "../modules/menubar.php";

if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
if (!$isPremium) {
    header("location: /music/advertisements.php");
}

// Display songs
$prepare = mysqli_prepare($con, "SELECT * FROM SONG");
$prepare -> execute();
$result = $prepare -> get_result();

echo "<table border='1'>
<tr>
<th>ID</th>
<th>Title</th>
<th>Duration</th>
</tr>";

// @todo: don't display the SongID here once managing songs is good to go
while($row = mysqli_fetch_array($result)) {
echo "<tr>
    <td>" . $row['SongID'] . "</td>
    <td>" . $row['Title'] . "</td>
    <td>" . $row['Duration'] . "</td>
    <td><a href='/music/song.php?SongID= " . $row['SongID'] . "'>View</a></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"play" . $row["SongID"] . "\" class=\"button\" value=\"Play\" />
    </form></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"queue" . $row["SongID"] . "\" class=\"button\" value=\"Add to Queue\" />
    </form></td>
    </tr>";
}
echo "</table>";

mysqli_close($con);
?>

<html>
    <head>
        <link href="../styles/style.css" rel="stylesheet" />
        <title>Songs - Spoofy</title>
    </head>
</html>
