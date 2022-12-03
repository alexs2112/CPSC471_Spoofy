<!-- @todo: We can turn this into an actual button at some point instead of a hyperlink -->
<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    $ArtistID = $_GET["ArtistID"];
	$AlbumID = $_GET["AlbumID"];
    $sql = "DELETE FROM HAS WHERE ArtistID=? AND AlbumID=?";
    $prepare = mysqli_prepare($con, $sql);
    if ($prepare) {
        $prepare -> bind_param("ss", $ArtistID, $AlbumID);
        $prepare -> execute();
    }
    $prepare -> close();
    header("location: edit_artist.php?ArtistID=" . $ArtistID . "");
} else {
    header("location: ../error.php");
}
?>
