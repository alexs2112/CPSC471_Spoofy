<html>
    <head>
        <title>View Song - Spoofy</title>
    </head>
    <body>
        <?php
            include "modules/menubar.php";
            include "modules/mysql_connect.php";

            $ID = $_GET["SongID"];

            // Perform mysql query
            $result = mysqli_query($con, "SELECT * FROM Song WHERE SongID=".$ID);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            // Display Song Details
            echo "<h1>".$row["Title"]."</h1>";
            echo "<p>Total Plays: ".($row["TotalPlays"] ?? "0")."</p>";
            echo "<p>Monthly Plays: ".($row["MonthlyPlays"] ?? "0")."</p>";
            echo "<p>Duration: ".$row["Duration"]."</p>";
            echo "<p>Music File: ".$row["MusicFile"]."</p>";

            // Retrieve Artist Details
            $result = mysqli_query($con, "SELECT ArtistID FROM Writes WHERE SongID=".$ID);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $artistID = $row["ArtistID"];
            $result = mysqli_query($con, "SELECT * FROM Artist WHERE ArtistID=".$artistID);

            // Display Artist Details
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo "<p>Artist: ".$row["Name"];
            };

            mysqli_close($con);
        ?>
    </body>
</html>
