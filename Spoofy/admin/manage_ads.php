<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    // Define variables and initialize with empty values
    $duration = "";
    $company = "";
    $soundfile = "";
    $error_string = "";

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Validate duration
        $duration = trim($_POST["duration"]);
        if(empty($duration)) {
            $error_string = "duration can't be empty.";
        } elseif(!preg_match('/^[0-9:]+$/', trim($_POST["duration"]))) {
            $error_string = "duration can only contain numbers and colons.";
        }
        
        // Validate company
        $company = trim($_POST["company"]);
        if(empty($company)) {
            $error_string = "company can't be empty.";
        } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["company"]))) {
            $error_string = "company can only contain letters and numbers";
        }
        
        // Validate soundfile
        $soundfile = trim($_POST["soundfile"]);
        if(empty($soundfile)) {
            $error_string = "company can't be empty.";
        } elseif(!preg_match('/^[a-zA-Z0-9_\/]+$/', trim($_POST["soundfile"]))) {
            $error_string = "soundfile can only contain letters, numbers, underscores, and forward slashes";
        }

        // If there are no errors, insert into the database
        if(empty($error_string)) {
            
            // Prepare an insert statement
            $sql = "INSERT INTO ADVERTISEMENT (duration, company, soundfile) VALUES (?, ?, ?)";
            $prepare = mysqli_prepare($con, $sql);
            if($prepare) {
                $prepare -> bind_param("ss", $duration, $company, $soundfile);    //no idea what the ss does

                $prepare -> execute();
                $result = $prepare -> get_result();
                
                // Redirect to login page after registering
                header("location: manage_ads.php");
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
        <title>Manage Advertisements - Spoofy</title>
    </head>
    <body>
        <div class="wrapper">
        <p>Manage Advertisements...</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" class="form-control" value="<?php echo $duration; ?>">
                </div>    
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company" class="form-control" value="<?php echo $company; ?>">
                </div>
                <div class="form-group">
                    <label>SoundFile</label>
                    <input type="text" name="soundfile" class="form-control" value="<?php echo $soundfile; ?>">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-secondary ml-2" value="Reset">
                </div>
                <?php if ($error_string) echo "<p style=\"color:red;\">".$error_string."</p>";?>
            </form>
        </div>
    </body>
</html>
