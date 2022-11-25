<html>
    <head>
        <title>View Artist - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/menubar.php";
            include "../modules/mysql_connect.php";

            $ArtistID = $_GET["ArtistID"];

            // Perform mysql query
            $prepare = mysqli_prepare($con, "SELECT * FROM ARTIST WHERE ArtistID=?");
            $prepare -> bind_param("s", $ArtistID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            // Display Artist Details
            $row = mysqli_fetch_array($result);
            echo "<h1>".$row["Name"]."</h1>";
            echo "<p>Profile Picture: ".$row["ProfilePicture"]."</p>";
            echo "<p>Banner Photo: ".$row["BannerPhoto"]."</p>";
            echo "<p>About: ".$row["About"]."</p>";

            // Get all song IDs
            $prepare = mysqli_prepare($con, "SELECT SongID FROM WRITES WHERE ArtistID=?");
            $prepare -> bind_param("s", $ArtistID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            // Display Song Information
            echo "<table border='1'>
            <tr>
            <th>Song</th>
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
                <td><a href='/music/song.php?SongID= " . $details['SongID'] . "'>View</a></td>
                </tr>";
            }
            echo "</table>";

            // Do the same for Album
            $prepare = mysqli_prepare($con, "SELECT AlbumID FROM HAS WHERE ArtistID=?");
            $prepare -> bind_param("s", $ArtistID);
            $prepare -> execute();
            $result = $prepare -> get_result();

            echo "<table border='1'>
            <tr>
            <th>Album</th>
            <th>Release</th>
            </tr>";

            while($row = mysqli_fetch_array($result)) {
                $albumID = $row["AlbumID"];

                $prepare = mysqli_prepare($con, "SELECT * FROM ALBUM WHERE AlbumID=?");
                $prepare -> bind_param("s", $albumID);
                $prepare -> execute();
                $album = $prepare -> get_result();
                $details = mysqli_fetch_array($album);

                echo "<tr>
                <td>" . $details['Title'] . "</td>
                <td>" . $details['ReleaseDate'] . "</td>
                <td><a href='/music/album.php?AlbumID= " . $albumID . "'>View</a></td>
                </tr>";
            }

            $prepare -> close();
            mysqli_close($con);
        ?>
    </body>
</html>
