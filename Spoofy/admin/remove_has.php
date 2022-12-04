<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {	
	//title
	
	$ArtistID = $_GET["ArtistID"];
	$ArtistName = $_GET["ArtistName"];
	echo "<h2>Albums From " . $ArtistName . ":</h2>";
	
	// Get all album IDs
	$prepare = mysqli_prepare($con, "SELECT AlbumID FROM HAS WHERE ArtistID=?");
	$prepare -> bind_param("s", $ArtistID);
	$prepare -> execute();
	$result = $prepare -> get_result();

	// Display Song Information
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Title</th>
	</tr>";
	while($row = mysqli_fetch_array($result)) {
		$prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
		$prepare -> bind_param("s", $row["AlbumID"]);
		$prepare -> execute();
		$album = $prepare -> get_result();
		$details = mysqli_fetch_array($album);

		echo "<tr>
		<td>" . $details['AlbumID'] . "</td>
		<td>" . $details['Title'] . "</td>
		<td><a href='/admin/remove_album_from_artist.php?ArtistID=" . $ArtistID . "&AlbumID=" .  $details['AlbumID'] . "&ArtistName=" . $ArtistName . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>
		</tr>";
	}
	echo "</table>";
	
	echo "<h2>Songs From " . $ArtistName . ":</h2>";
	
	// Get all song IDs
	$prepare = mysqli_prepare($con, "SELECT SongID FROM WRITES WHERE ArtistID=?");
	$prepare -> bind_param("s", $ArtistID);
	$prepare -> execute();
	$result = $prepare -> get_result();

	// Display Song Information
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Title</th>
	</tr>";
	while($row = mysqli_fetch_array($result)) {
		$prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
		$prepare -> bind_param("s", $row["SongID"]);
		$prepare -> execute();
		$song = $prepare -> get_result();
		$details = mysqli_fetch_array($song);

		echo "<tr>
		<td>" . $details['SongID'] . "</td>
		<td>" . $details['Title'] . "</td>
		<td><a href='/admin/remove_song_from_artist.php?ArtistID=" . $ArtistID . "&SongID=" . $details['SongID'] . "&ArtistName=" . $ArtistName . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>
		</tr>";
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
	<body>
		<button onclick='location.href="manage_artists.php"' type='button'>
			Return to Manage Albums
		</button><br>
	</body>
</html>