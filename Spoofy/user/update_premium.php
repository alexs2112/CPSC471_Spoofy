<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"]) {
    $Premium = filter_var($_GET["Premium"], FILTER_VALIDATE_BOOLEAN);
    $UserID = $_SESSION["UserID"];

    if ($Premium) {
        $sql = "UPDATE USER SET IsPremium=TRUE, SubRenewDate=? WHERE UserID=?";
        $prepare = mysqli_prepare($con, $sql);
        if ($prepare) {
            $renew_date = date("Y-m-d", strtotime("+1 month"));
            $prepare -> bind_param("ss", $renew_date, $UserID);
            $prepare -> execute();
        }
        $prepare -> close();
    } else {
        $sql = "UPDATE USER SET IsPremium=FALSE, SubRenewDate=NULL WHERE UserID=?";
        $prepare = mysqli_prepare($con, $sql);
        if ($prepare) {
            $prepare -> bind_param("s", $UserID);
            $prepare -> execute();
        }
        $prepare -> close();
    }
    header("location: profile.php?UserID=".$UserID);
} else {
    // Someone not logged in is trying to update premium
    header("location: ../login.php");
}
?>
