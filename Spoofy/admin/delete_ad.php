<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    $ID = $_GET["AdID"];
    $sql = "DELETE FROM ADVERTISEMENT WHERE AdID=?";
    $prepare = mysqli_prepare($con, $sql);
    if ($prepare) {
        $prepare -> bind_param("s", $ID);
        $prepare -> execute();
    }
    $prepare -> close();
    header("location: manage_ads.php");
} else {
    header("location: ../error.php");
}
?>
