<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]){

	// Define variables and initialize with empty values
	$error_string = "";
	$title = "";
	$duration = "";
	$filepath = "";

	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){

		// If there are no errors, insert into the database
		if(empty($error_string)) {
			
			// Prepare an insert statement
			$sql = "INSERT INTO WRITES VALUES (?, ?)";
			$prepare = mysqli_prepare($con, $sql);
			if($prepare) {
				// Bind all values
				$prepare -> bind_param("ii", $title, $duration);
				$title = intval($title);
				$duration = intval($duration);
				$prepare -> execute();
				$result = $prepare -> get_result();
				
				// Redirect to login page after registering
				header("location: manage_songs.php");
				$prepare -> close();
			}
		}
		
		// Close connection
		mysqli_close($con);
	}
} else {
	header("location: ../error.php");
}
?>

<html>
    <head>
        <title>Add a Song - Spoofy</title>
    </head>
    <body>
        <div class="wrapper">
        <h2>Add a Song</h2>
        <p>Fill in song information:</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Song ID</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                </div>    
                <div class="form-group">
                    <label>Artist ID</label>
                    <input type="text" name="duration" class="form-control" value="<?php echo $duration; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
                <?php if ($error_string) echo "<p style=\"color:red;\">".$error_string."</p>";?>
				<button onclick='location.href="manage_songs.php"' type='button'>
					Return to Manage Songs
				</button><br>
            </form>
        </div>
    </body>
</html>
