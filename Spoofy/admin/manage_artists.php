<?php
	include "../modules/menubar.php";
	include "../modules/mysql_connect.php";

	//title
	echo "<h2>Manage Artists:</h2>";

	//links to manage albums and songs
	echo "<button onclick='location.href=\"manage_songs.php\"' type='button'>
		Manage Songs
	</button>\n";
	echo "<button onclick='location.href=\"manage_albums.php\"' type='button'>
		Manage Albums
	</button><br><br>";
	
	//fetch all songs
	$result = mysqli_query($con, "SELECT * FROM Artist");
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Name</th>
	<th>About</th>
	<th>Profile Picture</th>
	<th>Banner Picture</th>
	</tr>";

	while($row = mysqli_fetch_array($result)) {
		// @todo: add Admin to account type
		echo "<tr>
		<td>" . $row['ArtistID'] . "</td>
		<td>" . $row['Name'] . "</td>
		<td>" . $row['About'] . "</td>
		<td>" . $row['ProfilePicture'] . "</td>
		<td>" . $row['BannerPicture'] . "</td>";
		
		echo "<td><a href='/admin/delete_artist.php?ArtistID= " . $row['ArtistID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";
		"</tr>";
	}
	echo "</table>";

	mysqli_close($con);

?>

<html>
    <head>
        <title>Manage Music - Spoofy</title>
    </head>
</html>