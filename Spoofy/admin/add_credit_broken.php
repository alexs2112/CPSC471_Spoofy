<?php
include "../modules/mysql_connect.php";


if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
	//$ArtistID = "";
	$SongID = $_GET["SongID"];
	$Title = $_GET["Title"];
	
	//echo $SongID;
	
	//echo $SongID .  " " . $ArtistID;
	
	//echo gettype($SongID);
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$sql = "INSERT INTO WRITES VALUES(?,?)";
		$prepare = mysqli_prepare($con, $sql);
		if($prepare) {
			$SongID = intval($SongID);
			$ArtistID = intval($ArtistID);
			// Bind all values
			$prepare -> bind_param("ii", $SongID, $ArtistID);
			//echo mysql_errno();
			$prepare -> execute();
			//echo mysql_errno();
			$result = $prepare -> get_result();
			//echo mysql_errno();
			
			//header("location: manage_songs.php");
			$prepare -> close();
		}
	}
	
} else {
    header("location: ../error.php");
}
?>

<html>
    <head>
        <title>Manage Music - Spoofy</title>
    </head>
	<body>
		<div class="wrapper">
			<h2>Add Artist Credit For <?php echo $Title ?></h2>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group">
						<label>Artist ID:</label>
						<input type="text" name="ArtistID" class="form-control" value="<?php echo $ArtistID; ?>">
					</div>
					<div class="form-group">
						<input type="submit" class="btn btn-primary" value="Submit">
						<input type="reset" class="btn btn-secondary ml-2" value="Reset">
					</div>
					<button onclick='location.href="manage_songs.php"' type='button'>
						Return to Manage Songs
					</button><br>
				</form>
		</div>
		<?php echo "<h3>Artists:</h3>";
		
		$result = mysqli_query($con, "SELECT * FROM Artist");
		echo "<table border='1'>
		<th>ID</th>
		<th>Name</th>
		</tr>";

		while($row = mysqli_fetch_array($result)) {
			echo "<tr>
			<td>" . $row['ArtistID'] . "</td>
			<td>" . $row['Name'] . "</td>";
			
			"</tr>";
		}
		echo "</table>";
		
		mysqli_close($con);
		?>
	</body>
</html>