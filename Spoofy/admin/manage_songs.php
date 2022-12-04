<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {	
	//title
	echo "<h2>Manage Songs:</h2>";
	
	//links to artists and albums pages
	echo "<button onclick='location.href=\"manage_artists.php\"' type='button'>
		Manage Artists
	</button>&nbsp;";
	echo "<button onclick='location.href=\"manage_albums.php\"' type='button'>
		Manage Albums
	</button><br><br>";

	//add a song
	echo "<button onclick='location.href=\"add_song.php\"' type='button'>
		Add Song
	</button>&nbsp;";
	echo "<button onclick='location.href=\"add_song_to_album.php\"' type='button'>
		Add Song to Album
	</button>&nbsp;";
	echo "<button onclick='location.href=\"add_credit.php\"' type='button'>
		Add Artist Credit
	</button><br>";
	

	//fetch all songs
	$result = mysqli_query($con, "SELECT * FROM Song");
	echo "<table border='1'>
	<tr>
	<th>ID</th>
	<th>Title</th>
	<th>Monthly Plays</th>
	<th>Artist</th>
	<th>Album</th>
	</tr>";

	while($row = mysqli_fetch_array($result)) {
		// @todo: add Admin to account type
		echo "<tr>
		<td>" . $row['SongID'] . "</td>
		<td>" . $row['Title'] . "</td>";
		if($row['MonthlyPlays']=== null){
			echo "<td>0</td>";
		}
		else{
			echo "<td>" . $row['MonthlyPlays'] . "</td>";
		}
		//<td><a href='/user/profile.php?UserID= " . $row['UserID'] . "'>View</a></td>

		$sql = "SELECT * FROM WRITES, ARTIST WHERE SongID=? AND WRITES.ArtistID=ARTIST.ArtistID";
		$prepare = mysqli_prepare($con, $sql);
		if ($prepare) {
			$prepare -> bind_param("s", $row['SongID']);
			$prepare -> execute();
			$album = $prepare -> get_result();
			$row2 = mysqli_fetch_array($album);
			echo"<td>".$row2['Name']."</td>";
		}

		$sql = "SELECT * FROM ALBUM_CONTAINS, ALBUM WHERE SongID=? AND ALBUM_CONTAINS.AlbumID=ALBUM.AlbumID";
		$prepare = mysqli_prepare($con, $sql);
		if ($prepare) {
			$prepare -> bind_param("s", $row['SongID']);
			$prepare -> execute();
			$album = $prepare -> get_result();
			$row2 = mysqli_fetch_array($album);
			echo"<td>".$row2['Title']."</td>";
		}
		
		echo "<td><a href='/admin/delete_song.php?SongID= " . $row['SongID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";
		echo "<td><a href='/admin/clear_monthly.php?SongID= " . $row['SongID'] . "' onclick=\"return confirm('Are you sure?')\";>Reset Monthly Plays</a></td>";
		//echo "<td><a href='/admin/add_credit.php?SongID=" . $row['SongID'] . "&Title=" . $row['Title'] . "'>Add Artist Credit</a></td>";
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
		<link href="../styles/style.css" rel="stylesheet" />
        <title>Manage Music - Spoofy</title>
    </head>
</html>
