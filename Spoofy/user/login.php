<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
    header("location: /user/profile.php?UserID=".$_SESSION["UserID"]);
 }
 
// Define variables and initialize with empty values
$username = "";
$password = "";
$error_string = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if username is empty
    $username = trim($_POST["username"]);
    if(empty($username)) {
        $error_string = "Please enter username.";
    }
    
    // Check if password is empty
    $password = trim($_POST["password"]);
    if(empty($password)) {
        $error_string = "Please enter your password.";
    }
    
    // Validate credentials
    if(empty($error_string)) {
        // Prepare a select statement
        $sql = "SELECT * FROM USER WHERE Username = ?";
        $prepare = mysqli_prepare($con, $sql);
        if($prepare) {
            $prepare -> bind_param("s", $username);
            $prepare -> execute();
            $result = $prepare -> get_result();

            if ($result) {
                // If there is a result, that means there is a matching username
                if(mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result);
                    if ($row["PasswordHash"] == hash("sha256", $password)) {

                        // Basic user info
                        $_SESSION["LoggedIn"] = true;
                        $_SESSION["UserID"] = $row["UserID"];
                        $_SESSION["Username"] = $row["Username"];
                        $_SESSION["IsPremium"] = boolval($row["IsPremium"]);

                        // Set up the playlist
                        $_SESSION["Queue"] = null;
                        $_SESSION["SongIndex"] = 0;

                        // Set up the list of disabled stems
                        // This is an associative array, of [song_id => [array_of_stems]]
                        $_SESSION["DisabledStems"] = array();

                        // Set the session variable Admin if the user is an administrator
                        $_SESSION["Admin"] = false;
                        $sql = "SELECT * FROM ADMIN WHERE AdminID = ?";
                        $prepare = mysqli_prepare($con, $sql);
                        if ($prepare) {
                            $prepare -> bind_param("s", $row["UserID"]);
                            $prepare -> execute();
                            $result = $prepare -> get_result();
                            if ($result && mysqli_num_rows($result) == 1) {
                                $_SESSION["Admin"] = true;
                            }
                        }

                        header("location: /index.php");
                    } else {
                        $error_string = "Invalid username or password.";
                    }
                } else {
                    $error_string = "Invalid username or password.";
                }
            }
            $prepare -> close();
        }
    }
    
    // Close connection
    mysqli_close($con);
}
?>

<html>
    <head>
    <link href="/styles/style.css" rel="stylesheet" />
        <title>Login - Spoofy</title>
    </head>
    <body>
        <h1>User Login</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <input type="submit" class="btn btn-primary" value="Login">
            <?php if ($error_string) echo "<p style=\"color:red;\">".$error_string."</p>";?>
            <p>Don't have an account? <a href="/user/register.php">Register</a>.</p>
        </form>
    </body>
</html>
