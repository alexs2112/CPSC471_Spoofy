<html>
    <head>
        <title>View Song - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/menubar.php";
            include "../modules/mysql_connect.php";

            $SongID = $_GET["SongID"];

            // Perform mysql query
            $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
            $prepare -> bind_param("s", $SongID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            // Display Song Details
            $row = mysqli_fetch_array($result);
            echo "<h1>".$row["Title"]."</h1>";
            echo "<p>Total Plays: ".($row["TotalPlays"] ?? "0")."</p>";
            echo "<p>Monthly Plays: ".($row["MonthlyPlays"] ?? "0")."</p>";
            echo "<p>Duration: ".$row["Duration"]."</p>";
            echo "<p>Music File: ".$row["MusicFile"]."</p>";

            // Retrieve Artist Details
            $prepare = mysqli_prepare($con, "SELECT ArtistID FROM WRITES WHERE SongID=?");
            $prepare -> bind_param("s", $SongID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            while ($row = mysqli_fetch_array($result)) {
                $artistID = $row["ArtistID"];

                $prepare = mysqli_prepare($con, "SELECT * FROM Artist WHERE ArtistID=?");
                $prepare -> bind_param("s", $artistID);
                $prepare -> execute();
                $result = $prepare -> get_result();

                // Display Artist Details
                while ($row = mysqli_fetch_array($result)) {
                    echo "<p></p><a href='/music/artist.php?ArtistID=" . $artistID . "'>Artist: ".$row["Name"];
                };
            }

            // Do the same for Album
            $prepare = mysqli_prepare($con, "SELECT AlbumID FROM ALBUM_CONTAINS WHERE SongID=?");
            $prepare -> bind_param("s", $SongID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            while($row = mysqli_fetch_array($result)) {
                $albumID = $row["AlbumID"];

                $prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
                $prepare -> bind_param("s", $albumID);
                $prepare -> execute();
                $result = $prepare -> get_result();

                while ($row = mysqli_fetch_array($result)) {
                    echo "<p></p><a href=\"/music/album.php?AlbumID=".$albumID."\">Album: ".$row["Title"];
                };
            }

            $prepare -> close();
            mysqli_close($con);
        ?>
    </body>
</html>
