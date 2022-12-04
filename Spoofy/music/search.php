<?php
include "../modules/mysql_connect.php";
include "../modules/image_functions.php";

$query = "";
if (array_key_exists("query", $_GET)) { $query = $_GET["query"]; }
if (array_key_exists("query", $_POST)) { $query = $_POST["query"]; }
$query = trim($query);

// Free users can only access ads, premium users can access songs, albums, artists
if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];
$isLoggedIn = isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";

    if ($isPremium) {
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
    } else {
        // See if an ad is being interacted with
        $prepare = mysqli_prepare($con, "SELECT AdID FROM ADVERTISEMENT");
        $prepare -> execute();
        $result = $prepare -> get_result();
        while ($row = mysqli_fetch_array($result)) {
            if (array_key_exists("play_ad".$row["AdID"], $_POST)) {
                play_song($row["AdID"]);
            } else if (array_key_exists("queue_ad".$row["AdID"], $_POST)) {
                add_song_to_queue($row["AdID"]);
            }
        }
    }
}
include "../modules/menubar.php";

// Display the search form
echo '
<div class="wrap_form">
<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" class="centered_form" method="post">
    <div class="form-group">
        <label><h2 class="centered_text">Search</h2></label>
        <input type="text" name="query" class="form-control" value="'.$query.'" style="min-width:192px;" ">
    </div>
    <div class="form-group">
        <input type="submit" class="submitButton" value="Submit">
    </div>
</form></div>';

$query = "%".$query."%";

if ($isPremium || !$isLoggedIn) {
    // Get all songs that match the query
    $sql = "SELECT * FROM SONG WHERE Title LIKE ?";
    $prepare = mysqli_prepare($con, $sql);
    if($prepare) {
        $prepare -> bind_param("s", $query);
        $prepare -> execute();
        $result = $prepare -> get_result();

        echo "<h1 class='centered_text'>Songs</h1>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p class='centered_text'>No Results</p>";
        } else {
            // Display table information for songs
            echo "<table border='1' class='centered_table'>
            <tr>
            <th></th>
            <th>Title</th>
            <th>Duration</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td><img id='cover_thumb' src='/resources/" . song_cover($con, $row['SongID']) . "' alt='cover'></td>
                <td>" . $row['Title'] . "</td>
                <td>" . $row['Duration'] . "</td>
                <td><a href='/music/song.php?SongID=" . $row['SongID'] . "'>View</a></td>";
                
                if ($isLoggedIn) {
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
        }
    }

    // Get all albums that match the query
    $sql = "SELECT * FROM ALBUM WHERE Title LIKE ?";
    $prepare = mysqli_prepare($con, $sql);
    if($prepare) {
        $prepare -> bind_param("s", $query);
        $prepare -> execute();
        $result = $prepare -> get_result();

        echo "<h1 class='centered_text'>Albums</h1>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p class='centered_text'>No Results</p>";
        } else {
            // Display table information for albums
            echo "<table border='1' class='centered_table'>
            <tr>
            <th></th>
            <th>Title</th>
            <th>Release Date</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td><img id='cover_thumb' src='/resources/" . album_cover($con, $row['AlbumID']) . "' alt='cover'></td>
                <td>" . $row['Title'] . "</td>
                <td>" . $row['ReleaseDate'] . "</td>
                <td><a href='/music/album.php?AlbumID= " . $row['AlbumID'] . "'>View</a></td>";

                if ($isLoggedIn) {
                    echo "<td><form method=\"post\">
                        <input type=\"submit\" name=\"play_album" . $row["AlbumID"] . "\" class=\"playButton\" value=\"Play\" />
                    </form></td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    // Get all artists that match the query
    $sql = "SELECT * FROM ARTIST WHERE Name LIKE ?";
    $prepare = mysqli_prepare($con, $sql);
    if($prepare) {
        $prepare -> bind_param("s", $query);
        $prepare -> execute();
        $result = $prepare -> get_result();

        echo "<h1 class='centered_text'>Artists</h1>";

        if (mysqli_num_rows($result) < 1) {
            echo "<p class='centered_text'>No Results</p>";
        } else {
            // Display table information for artists
            echo "<table border='1' class='centered_table'>
            <tr>
            <th>Name</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td><img id='cover_thumb' src='/resources/" . artist_profile($con, $row['ArtistID']) . "' alt='profile'></td>
                <td>" . $row['Name'] . "</td>
                <td><a href='/music/artist.php?ArtistID= " . $row['ArtistID'] . "'>View</a></td>
                </tr>";
            }
            echo "</table>";
        }
    }
} else {
    // Get all ads that match the query
    $sql = "SELECT * FROM ADVERTISEMENT WHERE Company LIKE ?";
    $prepare = mysqli_prepare($con, $sql);
    if($prepare) {
        $prepare -> bind_param("s", $query);
        $prepare -> execute();
        $result = $prepare -> get_result();

        echo "<h1 class='centered_text'>Advertisements</h1>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p class='centered_text'>No Results</p>";
        } else {
            // Display table information for songs
            echo "<table border='1' class='centered_table'>
            <tr>
            <th>ID</th>
            <th>Company</th>
            <th>Duration</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td>" . $row["AdID"] . "</td>
                <td>" . $row["Company"] . "</td>
                <td>" . $row["Duration"] . "</td>
                <td><a href='/music/advertisement.php?AdID= " . $row['AdID'] . "'>View</a></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"play_ad" . $row["AdID"] . "\" class=\"button\" value=\"Play\" />
                </form></td>
                <td><form method=\"post\">
                    <input type=\"submit\" name=\"queue_ad" . $row["AdID"] . "\" class=\"button\" value=\"Add to Queue\" />
                </form></td>
                </tr>";
            }
            echo "</table>";
        }
    }
}

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
    <link href="/styles/style.css" rel="stylesheet" />
        <title>Search - Spoofy</title>
    </head>
</html>
