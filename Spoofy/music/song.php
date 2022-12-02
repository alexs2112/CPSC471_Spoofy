<?php
$SongID = $_GET["SongID"];

// Buttons to Add to Queue, Play Song
if(!isset($_SESSION)) { session_start(); }
if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";
    if (array_key_exists("PlaySong", $_POST)) {
        play_song($SongID);
    } else if (array_key_exists("AddToQueue", $_POST)) {
        add_song_to_queue($SongID);
    } else if (array_key_exists("AddToPlaylist", $_POST)) {
        header("location: /music/add_song.php?SongID=".$SongID);
    }
}

include "../modules/menubar.php";
include "../modules/mysql_connect.php";

// Make sure the user is a premium user
if (!array_key_exists("IsPremium", $_SESSION) || !$_SESSION["IsPremium"]) {
    header("location: /error.php");
}

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
$prepare -> bind_param("s", $SongID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Song Details
$row = mysqli_fetch_array($result);
$songTitle = $row["Title"];
echo "<h1>".$songTitle."</h1>";
echo "<p>Total Plays: ".($row["TotalPlays"] ?? "0")."</p>";
echo "<p>Monthly Plays: ".($row["MonthlyPlays"] ?? "0")."</p>";
echo "<p>Duration: ".$row["Duration"]."</p>";
echo "<p>Music File: ".$row["MusicFile"]."</p>";

// Retrieve Artist Details
$prepare = mysqli_prepare($con, "SELECT ArtistID FROM WRITES WHERE SongID=?");
$prepare -> bind_param("s", $SongID);
$prepare -> execute();
$result = $prepare -> get_result();

while ($row = mysqli_fetch_array($result)) {
    $artistID = $row["ArtistID"];

    $prepare = mysqli_prepare($con, "SELECT * FROM Artist WHERE ArtistID=?");
    $prepare -> bind_param("s", $artistID);
    $prepare -> execute();
    $result = $prepare -> get_result();

    // Display Artist Details
    while ($row = mysqli_fetch_array($result)) {
        echo "<p></p><a href='/music/artist.php?ArtistID=" . $artistID . "'>Artist: ".$row["Name"]."</a>";
    };
}

// Do the same for Album
$prepare = mysqli_prepare($con, "SELECT AlbumID FROM ALBUM_CONTAINS WHERE SongID=?");
$prepare -> bind_param("s", $SongID);
$prepare -> execute();
$result = $prepare -> get_result();

while($row = mysqli_fetch_array($result)) {
    $albumID = $row["AlbumID"];

    $prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
    $prepare -> bind_param("s", $albumID);
    $prepare -> execute();
    $result = $prepare -> get_result();
    $album = mysqli_fetch_array($result);
    echo "<p></p><a href=\"/music/album.php?AlbumID=".$albumID."\">Album: ".$album["Title"]."</a>";
}

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
        <title><?php echo $songTitle; ?> - Spoofy</title>
    </head>
    <body>
        <form method="post">
            <input type="submit" name="PlaySong" class="button" value="Play Song" />
            <input type="submit" name="AddToQueue" class="button" value="Add to Queue" />
            <input type="submit" name="AddToPlaylist" class="button" value="Add to Playlist" />
        </form>
    </body>
</html>
