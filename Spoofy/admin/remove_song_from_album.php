<!-- @todo: We can turn this into an actual button at some point instead of a hyperlink -->
<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    $SongID = $_GET["SongID"];
	$AlbumID = $_GET["AlbumID"];
    $sql = "DELETE FROM ALBUM_CONTAINS WHERE SongID=? AND AlbumID=?";
    $prepare = mysqli_prepare($con, $sql);
    if ($prepare) {
        $prepare -> bind_param("ss", $SongID, $AlbumID);
        $prepare -> execute();
    }
    $prepare -> close();
    header("location: remove_album_contains.php?AlbumID=" . $AlbumID . "");
} else {
    header("location: ../error.php");
}
?>
