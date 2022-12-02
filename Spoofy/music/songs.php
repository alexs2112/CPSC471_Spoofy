<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

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
    </tr>";
}
echo "</table>";

mysqli_close($con);
?>

<html>
    <head>
        <title>Songs - Spoofy</title>
    </head>
</html>
