<?php
include "../modules/menubar.php";

$query = "";
if (array_key_exists("query", $_GET)) { $query = $_GET["query"]; }
if (array_key_exists("query", $_POST)) { $query = $_POST["query"]; }
$query = trim($query);

// Display the search form
echo '
<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
    <div class="form-group">
        <label>Search</label>
        <input type="text" name="query" class="form-control" value="'.$query.'">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
    </div>
</form>';

$query = "%".$query."%";
include "../modules/mysql_connect.php";

// Free users can only access ads, premium users can access songs, albums, artists
if(!isset($_SESSION)) { session_start(); }
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];

if ($isPremium) {
    // Get all songs that match the query
    $sql = "SELECT * FROM SONG WHERE Title LIKE ?";
    $prepare = mysqli_prepare($con, $sql);
    if($prepare) {
        $prepare -> bind_param("s", $query);
        $prepare -> execute();
        $result = $prepare -> get_result();

        echo "<h3>Songs</h3>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p>No Results</p>";
        } else {
            // Display table information for songs
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
                <td><a href='/music/song.php?SongID=" . $row['SongID'] . "'>View</a></td>
                <td><a href='/music/add_song.php?SongID=" . $row['SongID'] . "'>Add to Playlist</a></td>
                </tr>";
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

        echo "<h3>Albums</h3>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p>No Results</p>";
        } else {
            // Display table information for songs
            echo "<table border='1'>
            <tr>
            <th>Title</th>
            <th>Release Date</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td>" . $row['Title'] . "</td>
                <td>" . $row['ReleaseDate'] . "</td>
                <td><a href='/music/album.php?AlbumID= " . $row['AlbumID'] . "'>View</a></td>
                </tr>";
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

        echo "<h3>Artists</h3>";

        if (mysqli_num_rows($result) < 1) {
            echo "<p>No Results</p>";
        } else {
            // Display table information for songs
            echo "<table border='1'>
            <tr>
            <th>Name</th>
            </tr>";

            // For each song, display their information
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>
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

        echo "<h3>Advertisements</h3>";
        if (mysqli_num_rows($result) < 1) {
            echo "<p>No Results</p>";
        } else {
            // Display table information for songs
            echo "<table border='1'>
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
                </tr>";
            }
            echo "</table>";
        }
    }
}
?>

<html>
    <head>
        <title>Search - Spoofy</title>
    </head>
</html>
