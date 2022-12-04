<?php
include "../modules/mysql_connect.php";
include "../modules/image_functions.php";

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

    // See if an album is being interacted with
    $prepare = mysqli_prepare($con, "SELECT AlbumID FROM ALBUM");
    $prepare -> execute();
    $result = $prepare -> get_result();
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists("play_album".$row["AlbumID"], $_POST)) {
            play_album($con, $row["AlbumID"]);
        }
    }
}
include "../modules/menubar.php";

$ArtistID = $_GET["ArtistID"];

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM ARTIST WHERE ArtistID=?");
$prepare -> bind_param("s", $ArtistID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Artist Details
$row = mysqli_fetch_array($result);
$artistName = $row["Name"];
echo "<h1>".$artistName."</h1>";
echo "<p><b>Profile Picture:</b> ".$row["ProfilePicture"]."</p>";
echo "<p><b>Banner Picture:</b> ".$row["BannerPicture"]."</p>";
echo "<p><b>About:</b> ".$row["About"]."</p>";
echo "<p><b>Total Plays:</b> ".($row["TotalPlays"] ?? "0")."</p>";
echo "<p><b>Monthly Plays:</b> ".($row["MonthlyPlays"] ?? "0")."</p>";

// Get all song IDs
$prepare = mysqli_prepare($con, "SELECT SongID FROM WRITES WHERE ArtistID=?");
$prepare -> bind_param("s", $ArtistID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Song Information
echo "<table border='1'>
<tr>
<th></th>
<th>Song</th>
<th>Duration</th>
</tr>";
while($row = mysqli_fetch_array($result)) {
    $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
    $prepare -> bind_param("s", $row["SongID"]);
    $prepare -> execute();
    $song = $prepare -> get_result();
    $details = mysqli_fetch_array($song);

    echo "<tr>
    <td><img id='cover_thumb' src='/resources/" . song_cover($con, $details['SongID']) . "' alt='cover'></td>
    <td>" . $details['Title'] . "</td>
    <td>" . $details['Duration'] . "</td>
    <td><a href='/music/song.php?SongID= " . $details['SongID'] . "'>View</a></td>";
    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<td><form method=\"post\">
            <input type=\"submit\" name=\"play" . $details["SongID"] . "\" class=\"button\" value=\"Play\" />
        </form></td>
        <td><form method=\"post\">
            <input type=\"submit\" name=\"queue" . $details["SongID"] . "\" class=\"button\" value=\"Add to Queue\" />
        </form></td>";
    }
    echo "</tr>";
}
echo "</table>";

// Do the same for Album
$prepare = mysqli_prepare($con, "SELECT AlbumID FROM HAS WHERE ArtistID=?");
$prepare -> bind_param("s", $ArtistID);
$prepare -> execute();
$result = $prepare -> get_result();

echo "<table border='1'>
<tr>
<th></th>
<th>Album</th>
<th>Release</th>
</tr>";

while($row = mysqli_fetch_array($result)) {
    $albumID = $row["AlbumID"];

    $prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
    $prepare -> bind_param("s", $albumID);
    $prepare -> execute();
    $album = $prepare -> get_result();
    $details = mysqli_fetch_array($album);

    echo "<tr>
    <td><img id='cover_thumb' src='/resources/" . album_cover($con, $details['AlbumID']) . "' alt='cover'></td>
    <td>" . $details['Title'] . "</td>
    <td>" . $details['ReleaseDate'] . "</td>
    <td><a href='/music/album.php?AlbumID= " . $albumID . "'>View</a></td>";
    if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
        echo "<td><form method=\"post\">
            <input type=\"submit\" name=\"play_album" . $details["AlbumID"] . "\" class=\"button\" value=\"Play\" />
        </form></td>";
    }
    echo "</tr>";
}

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
        <link href="/styles/style.css" rel="stylesheet" />
        <title><?php echo $artistName; ?> - Spoofy</title>
    </head>
</html>
