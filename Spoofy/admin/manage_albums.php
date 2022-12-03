<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
	//title
	echo "<h2>Manage Albums:</h2>";

	//links to manage artists and songs
	echo "<button onclick='location.href=\"manage_artists.php\"' type='button'>
		Manage Artists
	</button>\n";
	echo "<button onclick='location.href=\"manage_songs.php\"' type='button'>
		Manage Songs
	</button><br><br>";
	
	//fetch all albums
	$result = mysqli_query($con, "SELECT * FROM Album");
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Title</th>
	<th>Single?</th>
	<th>Cover Art</th>
	<th>Release Data</th>
	<th>Genre</th>
	<th>Number of Songs</th>
	<th>Duration</th>
	</tr>";

	while($row = mysqli_fetch_array($result)) {
		// @todo: add Admin to account type
		echo "<tr>
		<td>" . $row['AlbumID'] . "</td>
		<td>" . $row['Title'] . "</td>
		<td>" . $row['IsSingle'] . "</td>
		<td>" . $row['CoverArt'] . "</td>
		<td>" . $row['ReleaseDate'] . "</td>
		<td>" . $row['Genre'] . "</td>
		<td>" . $row['NumSongs'] . "</td>
		<td>" . $row['TotalDuration'] . "</td>";
		
		echo "<td><a href='/admin/delete_album.php?AlbumID= " . $row['AlbumID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";
		"</tr>";
	}
	echo "</table>";

	mysqli_close($con);
} else {
    header("location: ../error.php");
}
?>

<html>
    <head>
        <title>Manage Music - Spoofy</title>
    </head>
</html>