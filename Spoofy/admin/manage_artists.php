<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";
include "../modules/image_functions.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
	//title
	echo "<h2>Manage Artists:</h2>";

	//links to manage albums and songs
	echo "<button onclick='location.href=\"manage_songs.php\"' type='button'>
		Manage Songs
	</button>&nbsp;";
	echo "<button onclick='location.href=\"manage_albums.php\"' type='button'>
		Manage Albums
	</button><br><br>";
	
	//link to add artist
	echo "<button onclick='location.href=\"add_artist.php\"' type='button'>
		Add Artist
	</button>&nbsp;";
	
	//fetch all songs
	$result = mysqli_query($con, "SELECT * FROM Artist");
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Name</th>
	<th>About</th>
	<th>Profile Picture</th>
	<th>File Paths</th>
	</tr>";

	while($row = mysqli_fetch_array($result)) {
		// @todo: add Admin to account type
		echo "<tr>
		<td>" . $row['ArtistID'] . "</td>
		<td>" . $row['Name'] . "</td>
		<td>" . $row['About'] . "</td>
		<td><img id='cover_header' src='/resources/" . artist_profile($con, $row['ArtistID']) . "' alt='profile'></td>
		<td>" . $row['ProfilePicture'] . "\n" . $row['BannerPicture'] . "</td>";
		
		echo "<td><a href='/admin/delete_artist.php?ArtistID=" . $row['ArtistID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";
		echo "<td><a href='/admin/edit_artist.php?ArtistID=" . $row['ArtistID'] . "'>Edit</a></td>";
		echo "<td><a href='/admin/remove_has.php?ArtistID=" . $row['ArtistID'] . "&ArtistName=" . $row['Name'] . "'>Remove Songs or Albums</a></td>";
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
	<link href="/styles/style.css" rel="stylesheet" />
        <title>Manage Music - Spoofy</title>
    </head>
</html>
