<html>
    <head>
        <title>View Album - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/menubar.php";
            include "../modules/mysql_connect.php";

            $AlbumID = $_GET["AlbumID"];

            // Perform mysql query
            $prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
            $prepare -> bind_param("s", $AlbumID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            // Display Album Details
            $row = mysqli_fetch_array($result);
            echo "<h1>".$row["Title"]."</h1>";
            echo "<p>Cover Art: ".$row["CoverArt"]."</p>";
            if ($row["IsSingle"]) { echo "<p>Single</p>"; }
            echo "<p>Genre: ".$row["Genre"]."</p>";
            echo "<p>Release Date: ".$row["ReleaseDate"]."</p>";

            // Retrieve Artist Details
            $prepare = mysqli_prepare($con, "SELECT ArtistID FROM HAS WHERE AlbumID=?");
            $prepare -> bind_param("s", $AlbumID);
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

            // Get all song IDs
            $prepare = mysqli_prepare($con, "SELECT SongID FROM ALBUM_CONTAINS WHERE AlbumID=?");
            $prepare -> bind_param("s", $AlbumID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            // Display Song Information
            echo "<table border='1'>
            <tr>
            <th>Title</th>
            <th>Duration</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                $prepare = mysqli_prepare($con, "SELECT * FROM SONG WHERE SongID=?");
                $prepare -> bind_param("s", $row["SongID"]);
                $prepare -> execute();
                $song = $prepare -> get_result();
                $details = mysqli_fetch_array($song);

                echo "<tr>
                <td>" . $details['Title'] . "</td>
                <td>" . $details['Duration'] . "</td>
                <td><a href='/music/song.php?SongID=" . $details['SongID'] . "'>View</a></td>
                </tr>";
            }
            echo "</table>";

            $prepare -> close();
            mysqli_close($con);
        ?>
    </body>
</html>
