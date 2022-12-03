<?php
$AdID = $_GET["AdID"];

// Buttons to Add to Queue, Play Song
if(!isset($_SESSION)) { session_start(); }
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists("PlaySong", $_POST)) {
        $_SESSION["Queue"] = array($AdID);
        $_SESSION["SongIndex"] = 0;
    } else if (array_key_exists("AddToQueue", $_POST)) {
        if ($_SESSION["Queue"] == null) {
            $_SESSION["Queue"] = array();
            $_SESSION["SongIndex"] = 0;
        }
        array_push($_SESSION["Queue"], $AdID);
    }
}

include "../modules/menubar.php";
include "../modules/mysql_connect.php";

// Make sure the user is a free user
if (array_key_exists("IsPremium", $_SESSION) && $_SESSION["IsPremium"]) {
    header("location: /index.php");
}

// Perform mysql query
$prepare = mysqli_prepare($con, "SELECT * FROM ADVERTISEMENT WHERE AdID=?");
$prepare -> bind_param("s", $AdID);
$prepare -> execute();
$result = $prepare -> get_result();

// Display Ad Details
$row = mysqli_fetch_array($result);
$adTitle = $row["Company"];
echo "<h1>".$adTitle."</h1>";
echo "<p>Duration: ".$row["Duration"]."</p>";
echo "<p>Sound File: ".$row["SoundFile"]."</p>";

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
        <link href="../styles/style.css" rel="stylesheet" />
        <title><?php echo $adTitle; ?> - Spoofy</title>
    </head>
    <body>
        <form method="post">
            <input type="submit" name="PlaySong" class="button" value="Play Advertisement" />
            <input type="submit" name="AddToQueue" class="button" value="Add to Queue" />
        </form>
    </body>
</html>
