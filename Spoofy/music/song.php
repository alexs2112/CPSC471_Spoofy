<?php
$SongID = $_GET["SongID"];

// Buttons to Add to Queue, Play Song
if(!isset($_SESSION)) { session_start(); }
if (array_key_exists("PlaySong", $_POST)) {
    $_SESSION["Queue"] = array($SongID);
    $_SESSION["SongIndex"] = 0;
} else if (array_key_exists("AddToQueue", $_POST)) {
    if ($_SESSION["Queue"] == null) {
        $_SESSION["Queue"] = array();
        $_SESSION["SongIndex"] = 0;
    }
    array_push($_SESSION["Queue"], $SongID);
}

include "../modules/menubar.php";
include "../modules/mysql_connect.php";

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
$prepare -> bind_param("s", $SongID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Song Details
$row = mysqli_fetch_array($result);
echo "<h1>".$row["Title"]."</h1>";
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
        <title>View Song - Spoofy</title>
    </head>
    <body>
        <form method="post">
            <input type="submit" name="PlaySong" class="button" value="Play Song" />
            <input type="submit" name="AddToQueue" class="button" value="Add to Queue" />
        </form>
    </body>
</html>
