<html>
    <head>
        <title>Songs - Spoofy</title>
    </head>
    <body>
        <?php
            include "modules/menubar.php";
            include "modules/mysql_connect.php";

            // Display songs
            $prepare = mysqli_prepare($con, "SELECT * FROM SONG");
            $prepare -> execute();
            $result = $prepare -> get_result();

            echo "<table border='1'>
            <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Duration</th>
            </tr>";

            while($row = mysqli_fetch_array($result)) {
                echo "<tr>
                <td>" . $row['SongID'] . "</td>
                <td>" . $row['Title'] . "</td>
                <td>" . $row['Duration'] . "</td>
                <td><a href='music/song.php?SongID= " . $row['SongID'] . "'>View</a></td>
                </tr>";
            }
            echo "</table>";

            mysqli_close($con);
        ?>
    </body>
</html>