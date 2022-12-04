<?php
include "../modules/mysql_connect.php";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../modules/queue_functions.php";

    // See if a song is being interacted with
    $prepare = mysqli_prepare($con, "SELECT AdID FROM ADVERTISEMENT");
    $prepare -> execute();
    $result = $prepare -> get_result();
    while ($row = mysqli_fetch_array($result)) {
        if (array_key_exists("play".$row["AdID"], $_POST)) {
            play_song($row["AdID"]);
        } else if (array_key_exists("queue".$row["AdID"], $_POST)) {
            add_song_to_queue($row["AdID"]);
        }
    }
}
include "../modules/menubar.php";

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

while($row = mysqli_fetch_array($result)) {
echo "<tr>
    <td>" . $row['AdID'] . "</td>
    <td>" . $row['Company'] . "</td>
    <td>" . $row['Duration'] . "</td>
    <td><a href='/music/advertisement.php?AdID= " . $row['AdID'] . "'>View</a></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"play" . $row["AdID"] . "\" class=\"button\" value=\"Play\" />
    </form></td>
    <td><form method=\"post\">
        <input type=\"submit\" name=\"queue" . $row["AdID"] . "\" class=\"button\" value=\"Add to Queue\" />
    </form></td>
    </tr>";
}
echo "</table>";

$prepare -> close();
mysqli_close($con);
?>

<html>
    <head>
    <link href="/styles/style.css" rel="stylesheet" />
        <title>Advertisements - Spoofy</title>
    </head>
</html>
