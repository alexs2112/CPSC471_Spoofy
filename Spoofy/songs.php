<html>
    <head>
        <title>Songs - Spoofy</title>
    </head>
    <body>
        <?php
            include "modules/menubar.php";
            include "modules/mysql_connect.php";

            // Display songs
            $result = mysqli_query($con, "SELECT * FROM Song");

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
                <td><a href='view_song.php?SongID= " . $row['SongID'] . "'>View</a></td>
                </tr>";
            }
            echo "</table>";

            mysqli_close($con);
        ?>
    </body>
</html>