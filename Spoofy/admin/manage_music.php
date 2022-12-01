<html>
    <head>
        <title>Manage Music - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/mysql_connect.php";

            if(!isset($_SESSION)) { session_start(); }
            if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
                //add or remove songs, albums, artists
                //add songs to albums, add albums to artists
            } else {
                header("location: ../error.php");
            }
        ?>
        <p>Manage Music...</p>
    </body>
</html>
