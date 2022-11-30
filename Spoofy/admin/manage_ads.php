<html>
    <head>
        <title>Manage Ads - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/mysql_connect.php";

            if(!isset($_SESSION)) { session_start(); }
            if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
                //add or remove ads
            } else {
                header("location: ../error.php");
            }
        ?>
        <p>Manage Advertisements...</p>
    </body>
</html>
