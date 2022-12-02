<?php
include "../modules/mysql_connect.php";
$AlbumID = $_GET["AlbumID"];

// Button to Play Album
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

    if (array_key_exists("PlayAlbum", $_POST)) {
        play_album($con, $AlbumID);
    }
}

include "../modules/menubar.php";

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
$prepare -> bind_param("s", $AlbumID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Album Details
$row = mysqli_fetch_array($result);
$albumTitle = $row["Title"];
echo "<h1>".$albumTitle."</h1>";
echo "<p>Cover Art: ".$row["CoverArt"]."</p>";
if ($row["IsSingle"]) { echo "<p>Single</p>"; }
echo "<p>Genre: ".$row["Genre"]."</p>";
echo "<p>Release Date: ".$row["ReleaseDate"]."</p>";

// Retrieve Artist Details
$prepare = mysqli_prepare($con, "SELECT ArtistID FROM HAS WHERE AlbumID=?");
$prepare -> bind_param("s", $AlbumID);
$prepare -> execute();
$result = $prepare -> get_result();

while ($row = mysqli_fetch_array($result)) {
    $artistID = $row["ArtistID"];

    $prepare = mysqli_prepare($con, "SELECT * FROM Artist WHERE ArtistID=?");
    $prepare -> bind_param("s", $artistID);
    $prepare -> execute();
    $result = $prepare -> get_result();

    // Display Artist Details
    while ($artist = mysqli_fetch_array($result)) {
        echo "<p></p><a href='/music/artist.php?ArtistID=" . $artistID . "'>Artist: ".$artist["Name"]."</a>";
    };
}

// Get all song IDs
$prepare = mysqli_prepare($con, "SELECT SongID FROM ALBUM_CONTAINS WHERE AlbumID=?");
$prepare -> bind_param("s", $AlbumID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Song Information
echo "<table border='1'>
<tr>
<th>Title</th>
<th>Duration</th>
</tr>";
while($row = mysqli_fetch_array($result)) {
    $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
    $prepare -> bind_param("s", $row["SongID"]);
    $prepare -> execute();
    $song = $prepare -> get_result();
    $details = mysqli_fetch_array($song);

    echo "<tr>
    <td>" . $details['Title'] . "</td>
    <td>" . $details['Duration'] . "</td>
    <td><a href='/music/song.php?SongID=" . $details['SongID'] . "'>View</a></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"play" . $row["SongID"] . "\" class=\"button\" value=\"Play\" />
    </form></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"queue" . $row["SongID"] . "\" class=\"button\" value=\"Add to Queue\" />
    </form></td>
    </tr>";
}
echo "</table>";

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
        <title><?php echo $albumTitle; ?> - Spoofy</title>
    </head>
    <body>
        <form method="post">
            <input type="submit" name="PlayAlbum" class="button" value="Play Album" />
        </form>
    </body>
</html>
