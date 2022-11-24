<html>
    <head>
        <title>Manage Users - Spoofy</title>
    </head>
    <body>
        <?php
            include "../modules/menubar.php";
            include "../modules/mysql_connect.php";
            
            echo "<h2>Manage Users:</h2>";

            $result = mysqli_query($con, "SELECT * FROM User");
            echo "<table border='1'>
            <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Type</th>
            <th>Renewal Date</th>
            <th>Profile</th>
            </tr>";

            while($row = mysqli_fetch_array($result)) {
                // @todo: add Admin to account type
                echo "<tr>
                <td>" . $row['UserID'] . "</td>
                <td>" . $row['Username'] . "</td>
                <td>" . ($row['IsPremium'] ? "Premium" : "Free") . "</td>
                <td>" . $row['SubRenewDate'] . "</td>
                <td><a href='/user/profile.php?UserID= " . $row['UserID'] . "'>View</a></td>
                <td><a href='/admin/delete_user.php?UserID= " . $row['UserID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>
                </tr>";
            }
            echo "</table>";

            mysqli_close($con);
        ?>
    </body>
</html>
