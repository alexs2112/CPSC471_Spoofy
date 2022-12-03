<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
$numSongs = (!isset($_SESSION["Queue"]) || $_SESSION["Queue"] == null) ? 0 : count($_SESSION["Queue"]);

// Handle button presses for individual indices
if($_SERVER["REQUEST_METHOD"] == "POST") {
    for ($i = 0; $i < $numSongs; $i++) {
        if (array_key_exists("Play".$i, $_POST)) {
            $_SESSION["SongIndex"] = $i;
        } else if (array_key_exists("Remove".$i, $_POST)) {
            if ($_SESSION["SongIndex"] > $i) { $_SESSION["SongIndex"]--; }
            unset($_SESSION["Queue"][$i]);
            $_SESSION["Queue"] = array_values($_SESSION["Queue"]);  // https://stackoverflow.com/a/369761

            if (count($_SESSION["Queue"]) == 0) { $_SESSION["Queue"] = null; }
            else if ($_SESSION["SongIndex"] >= count($_SESSION["Queue"])) { $_SESSION["SongIndex"] = count($_SESSION["Queue"]) - 1; }
        }
    }
}
include "../modules/menubar.php";

// Title
echo "<h1>Music Queue</h1>";

// Do this again after menubar, clearing the queue in menubar can do weird stuff
$numSongs = (!isset($_SESSION["Queue"]) || $_SESSION["Queue"] == null) ? 0 : count($_SESSION["Queue"]);
$isPremium = array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"];

// Display song information
if ($numSongs > 0) {
    if ($isPremium) {
        echo "<table border='1'>
        <tr>
        <th>Title</th>
        <th>Duration</th>
        </tr>";
    } else {
        echo "<table border='1'>
        <tr>
        <th>Company</th>
        <th>Duration</th>
        </tr>";
    }

    for ($i = 0; $i < $numSongs; $i++) {
        // Get the songs information
        $songID = $_SESSION["Queue"][$i];

        // Free users can only play ads, premium users can play songs
        if ($isPremium) {
            $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
        } else {
            $prepare = mysqli_prepare($con, "SELECT * FROM ADVERTISEMENT WHERE AdID=?");
        }
        $prepare -> bind_param("s", $songID);
        $prepare -> execute();
        $result = $prepare -> get_result();
        $row = mysqli_fetch_array($result);

        $title = $isPremium ? $row['Title'] : $row['Company'];
        echo "<tr>";

        // Bold the title if it is currently playing
        echo $i == $_SESSION["SongIndex"] ? "<td><b>" . $title . "</b></td>" : "<td>" . $title . "</td>";
        echo "<td>" . $row['Duration'] . "</td>";
        if ($isPremium) { echo "<td><a href='/music/song.php?SongID= " . $row['SongID'] . "'>View</a></td>"; }
        echo "<td>
            <form method=\"post\">
                <input type=\"submit\" name=\"Play".$i."\" class=\"button\" value=\"Play\" />
            </form>
        </td>
        <td>
            <form method=\"post\">
                <input type=\"submit\" name=\"Remove".$i."\" class=\"button\" value=\"Remove\" />
            </form>
        </td>
        </tr>";
    }
    echo "</table>";

    // Display shuffle and clear buttons, these call 'POST' and are handled when menubar is included
    echo "
    <form method=\"post\">
        <input type=\"submit\" name=\"ClearQueue\" class=\"button\" value=\"Clear Queue\" />
        <input type=\"submit\" name=\"Shuffle\" class=\"button\" value=\"Shuffle Queue\" />
    </form>";
} else {
    echo "<h3>No songs playing.</h3>";
}
?>

<html>
    <head>
        <link href="../styles/style.css" rel="stylesheet" />
        <title>Queue - Spoofy</title>
    </head>
</html>
