<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {	
	//title
	echo "<h2>Songs in Album:</h2>";
	
	$AlbumID = $_GET["AlbumID"];
	
	// Get all song IDs
	$prepare = mysqli_prepare($con, "SELECT SongID FROM ALBUM_CONTAINS WHERE AlbumID=?");
	$prepare -> bind_param("s", $AlbumID);
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
		<td><a href='/admin/remove_song_from_album.php?SongID=" . $row['SongID'] . "&AlbumID=" . $AlbumID . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>
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
		<button onclick='location.href="manage_albums.php"' type='button'>
			Return to Manage Albums
		</button><br>
	</body>
</html>