<?php
$SongID = $_GET["SongID"];
include "../modules/mysql_connect.php";
include "../modules/image_functions.php";
if(!isset($_SESSION)) { session_start(); }

// Some functions to enable and disable functions
function enable_stem($stemNo) {
    if (!array_key_exists("DisabledStems", $_SESSION)) { return; }
    global $SongID;

    // If $songID => [$stemNo] exists, find and unset it
    if (array_key_exists($SongID, $_SESSION["DisabledStems"])) {
        $i = array_search($stemNo, $_SESSION["DisabledStems"][$SongID]);
        if ($i !== false) {
            unset($_SESSION["DisabledStems"][$SongID][$i]);
        }

        // If this songID is fully enabled, remove it from the list of stems
        // @todo this doesn't actually work, it doesn't really matter though
        if (count($_SESSION["DisabledStems"][$SongID]) == 0) {
            $i = array_search($SongID, $_SESSION["DisabledStems"]);
            if ($i !== false) {
                unset($_SESSION["DisabledStems"][$i]);
            }
        }
    }
}
function disable_stem($stemNo) {
    if (!array_key_exists("DisabledStems", $_SESSION)) { return; }
    global $SongID;

    // If $songID is not in DisabledStems yet, create it with this stem disabled
    if (!array_key_exists($SongID, $_SESSION["DisabledStems"])) {
        $_SESSION["DisabledStems"][$SongID] = array($stemNo);
    } else {
        // Else, add this stem to the songID
        array_push($_SESSION["DisabledStems"][$SongID], $stemNo);
    }
}
function is_stem_disabled($stemNo) {
    if (!array_key_exists("DisabledStems", $_SESSION)) { return false; }
    global $SongID;
    return array_key_exists($SongID, $_SESSION["DisabledStems"]) && in_array($stemNo, $_SESSION["DisabledStems"][$SongID]);
}
function print_stems() {
    // Debugging help
    if (!array_key_exists("DisabledStems", $_SESSION)) { echo "<h1>Disabled Stems not in SESSION</h1>"; return; }

    if (count($_SESSION["DisabledStems"]) == 0) { echo "<h1>No songs with disabled stems</h1>"; return; }
    foreach($_SESSION["DisabledStems"] as $songID) {
        if (count($songID) == 0) { echo "<h1>Error: No disabled stems</h1>"; }
        else {
            echo "<h3>Song</h3>";
            echo "<ul>";
            foreach($songID as $stemNo) {
                echo "<li>".$stemNo."</li>";
            }
            echo "</ul>";
        }
    }
}

// Buttons functionalities
if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";
    if (array_key_exists("PlaySong", $_POST)) {
        play_song($SongID);
        increment_song_plays($con, $SongID);
    } else if (array_key_exists("AddToQueue", $_POST)) {
        add_song_to_queue($SongID);
    } else if (array_key_exists("AddToPlaylist", $_POST)) {
        header("location: /music/add_song.php?SongID=".$SongID);
    } else {
        // See if a stem is being enabled/disabled
        $prepare = mysqli_prepare($con, "SELECT StemNo FROM STEM WHERE SongID=?");
        $prepare -> bind_param("s", $SongID);
        $prepare -> execute();
        $result = $prepare -> get_result();
        while ($row = mysqli_fetch_array($result)) {
            if (array_key_exists("enable".$row["StemNo"], $_POST)) {
                is_stem_disabled($row["StemNo"]) ? enable_stem($row["StemNo"]) : disable_stem($row["StemNo"]);
            }
        }
    }
}
include "../modules/menubar.php";

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
$prepare -> bind_param("s", $SongID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Song Details
$row = mysqli_fetch_array($result);
$songTitle = $row["Title"];

echo "
<div class='flex_container'>
    <div>
        <ul class='invisible'>
            <li><h1>".$songTitle."</h1></li>
            <li><b>Total Plays:</b> ".($row["TotalPlays"] ?? "0")."</li>
            <li><b>Monthly Plays:</b> ".($row["MonthlyPlays"] ?? "0")."</li>
            <li><b>Duration:</b> ".$row["Duration"]."</li>
        </ul>
    </div>
    <div>
        <img id='cover_header' src='/resources/" . song_cover($con, $SongID) . "' alt='cover' />
    </div>
</div>";
echo "<p><b>Music File:</b> ".$row["MusicFile"]."</p>";

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

if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {

    // Buttons to Play, Add to Queue, Add to Playlist
    echo '
    <form method="post">
        <input type="submit" name="PlaySong" class="button" value="Play Song" />
        <input type="submit" name="AddToQueue" class="button" value="Add to Queue" />
        <input type="submit" name="AddToPlaylist" class="button" value="Add to Playlist" />
    </form>
    ';

    // Display the stems, with buttons to disable/enable them
    $prepare = mysqli_prepare($con, "SELECT StemNo, Musicfile FROM STEM WHERE SongID=?");
    $prepare -> bind_param("s", $SongID);
    $prepare -> execute();
    $result = $prepare -> get_result();

    echo "<h3>Stems:</h3>";
    echo "<table border='1'>
    <tr>
    <th>Stem</th>
    <th>File</th>
    </tr>";
    while($row = mysqli_fetch_array($result)) {
        echo "<tr>
            <td>" . $row['StemNo'] . "</td>
            <td>" . $row['Musicfile'] . "</td>";
        echo "<td><form method=\"post\">
            <input type=\"submit\" name=\"enable" . $row["StemNo"] . "\" class=\"button\" value=\"".(is_stem_disabled($row['StemNo']) ? "Enable" : "Disable")."\" />
        </form></td>";
        echo "</tr>";
    }
}

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
    <link href="/styles/style.css" rel="stylesheet" />
        <title><?php echo $songTitle; ?> - Spoofy</title>
    </head>
</html>
