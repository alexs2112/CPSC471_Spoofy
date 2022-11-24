<html>
    <head>
        <title>CPSC 471 - Spoofy</title>
    </head>
    <body>
    <?php include "modules/menubar.php"; ?>
    <?php 
        session_start();
        if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) { 
            echo "<h1>Welcome ".$_SESSION["Username"]."!</h1>";
        } else {
            echo "<h1>Welcome to Spoofy!</h1>";
        }
    ?>

    <!-- @todo Eventually move this to a dedicated Login/Register page -->
    <a href="/user/login.php">Log In</a>
    <a href="/user/register.php">Register</a>
    </body>
</html>
