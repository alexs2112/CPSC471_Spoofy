<?php
include "../modules/menubar.php";
include "../modules/mysql_connect.php";

// Display ads
$prepare = mysqli_prepare($con, "SELECT * FROM ADVERTISEMENT");
$prepare -> execute();
$result = $prepare -> get_result();

echo "<table border='1'>
<tr>
<th>ID</th>
<th>Company</th>
<th>Duration</th>
</tr>";

// @todo: don't display the SongID here once managing songs is good to go
while($row = mysqli_fetch_array($result)) {
echo "<tr>
    <td>" . $row['AdID'] . "</td>
    <td>" . $row['Company'] . "</td>
    <td>" . $row['Duration'] . "</td>
    <td><a href='/music/advertisement.php?AdID= " . $row['AdID'] . "'>View</a></td>
    </tr>";
}
echo "</table>";

mysqli_close($con);
?>

<html>
    <head>
        <title>Advertisements - Spoofy</title>
    </head>
</html>
