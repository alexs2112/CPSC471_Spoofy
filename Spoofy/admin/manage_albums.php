<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";
include "../modules/image_functions.php";

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
	
	echo "<button onclick='location.href=\"add_album.php\"' type='button'>
		Add Album
	</button>&nbsp;";
	echo "<button onclick='location.href=\"add_album_credit.php\"' type='button'>
		Add Album Credit
	</button><br>";
	
	//fetch all albums
	$result = mysqli_query($con, "SELECT * FROM Album");
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Title</th>
	<th>Artist Name</th>
	<th>Single?</th>
	<th>Cover Art</th>
	<th>Release Date</th>
	<th>Genre</th>
	</tr>";

	while($row = mysqli_fetch_array($result)) {
		// @todo: add Admin to account type
		echo "<tr>
		<td>" . $row['AlbumID'] . "</td>
		<td>" . $row['Title'] . "</td>";
		
		$sql = "SELECT * FROM HAS, ARTIST WHERE AlbumID=? AND HAS.ArtistID=ARTIST.ArtistID";
		$prepare = mysqli_prepare($con, $sql);
		if ($prepare) {
			$prepare -> bind_param("s", $row['AlbumID']);
			$prepare -> execute();
			$album = $prepare -> get_result();
			$row2 = mysqli_fetch_array($album);
			echo"<td>".$row2['Name']."</td>";
		}

		echo "<td>" . $row['IsSingle'] . "</td>
		<td><p>" . $row['CoverArt'] . "</p>
		<img id='cover_thumb' src='/resources/" . album_cover($con, $row['AlbumID']) . "' alt='cover'></td>
		<td>" . $row['ReleaseDate'] . "</td>
		<td>" . $row['Genre'] . "</td>";
		
		echo "<td><a href='/admin/delete_album.php?AlbumID= " . $row['AlbumID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";
		echo "<td><a href='/admin/edit_album.php?AlbumID=" . $row['AlbumID'] . "'>Edit</a></td>";
		echo "<td><a href='/admin/remove_album_contains.php?AlbumID=" . $row['AlbumID'] . "'>Remove Song</a></td>";
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
