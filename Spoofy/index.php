<html>
    <head>
        <title>Spoofy - CPSC 471</title>
    </head>
    <body>
        <h2>Hello?</h2>
        <?php echo "<p>Hello World</p>"; ?>
        <?php
            $servername = "localhost";
            $username = "spoofyUser";
            $password = "testing";
            $database = "SpoofyDB";

            // Create connection
            $conn = mysqli_connect($servername, $username, $password, $database);

            // Check connection
            if (!$conn) {
                die("<p>Connection failed: " . mysqli_connect_error() . "</p>");
            }
            echo "<p>Connected successfully</p>";
        ?>
    </body>
</html>
