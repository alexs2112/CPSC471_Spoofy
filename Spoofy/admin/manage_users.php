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
    <td><a href='/admin/delete_user.php?UserID= " . $row['UserID'] . "' onclick=\"return confirm('Are you sure?')\";>Delete</a></td>";

    // Determine admin status
    $sql = "SELECT AdminID FROM ADMIN WHERE AdminID=?";
    $prepare = mysqli_prepare($con, $sql);
    if ($prepare) {
        $prepare -> bind_param("s", $row['UserID']);
        $prepare -> execute();
        $is_admin = $prepare -> get_result();
        $make_admin = mysqli_num_rows($is_admin) == 0;

        // @todo Make these nice little icons
        echo "<td><a href='/admin/adminship.php?UserID= " . $row['UserID'] . "&Admin=".($make_admin)."' onclick=\"return confirm('Are you sure?')\";>".($make_admin ? "Adminify" : "Userify")."</a></td>";
    }

    "</tr>";
}
echo "</table>";

mysqli_close($con);
?>

<html>
    <head>
        <title>Manage Users - Spoofy</title>
    </head>
</html>
