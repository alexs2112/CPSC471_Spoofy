<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$SongID = $_POST["SongID"];
		$AlbumID = $_POST["AlbumID"];

		$sql = "INSERT INTO ALBUM_CONTAINS VALUES(?,?)";
		$prepare = mysqli_prepare($con, $sql);
		if($prepare) {
			// Bind all values
			$prepare -> bind_param("ss", $AlbumID, $SongID);
			$prepare -> execute();
			$result = $prepare -> get_result();
			
			header("location: manage_songs.php");
			$prepare -> close();
		}
	}
} else {
    header("location: ../error.php");
}
?>
<html>
    <head>
        <title>Add Writes - Spoofy</title>
    </head>
    <body>
        <h1>Add Song to Album</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>SongID</label>
                <input type="text" name="SongID" class="form-control">
            </div>   
            <div class="form-group">
                <label>AlbumID</label>
                <input type="text" name="AlbumID" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add to Album">
            </div>
			<button onclick='location.href="manage_songs.php"' type='button'>
				Return to Manage Songs
			</button><br>
        </form>
		<?php echo "<h3>Songs:</h3>";
		
		$result = mysqli_query($con, "SELECT * FROM Song");
		echo "<table border='1'>
		<th>ID</th>
		<th>Title</th>
		</tr>";

		while($row = mysqli_fetch_array($result)) {
			echo "<tr>
			<td>" . $row['SongID'] . "</td>
			<td>" . $row['Title'] . "</td>";
			
			"</tr>";
		}
		echo "</table>";
		
		echo "<h3>Albums:</h3>";
		
		$result2 = mysqli_query($con, "SELECT * FROM Album");
		echo "<table border='1'>
		<th>ID</th>
		<th>Title</th>
		</tr>";

		while($row2 = mysqli_fetch_array($result2)) {
			echo "<tr>
			<td>" . $row2['AlbumID'] . "</td>
			<td>" . $row2['Title'] . "</td>";
			
			"</tr>";
		}
		echo "</table>";
		
		mysqli_close($con);
		?>
    </body>
</html>