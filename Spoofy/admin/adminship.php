<?php
include "../modules/mysql_connect.php";

if(!isset($_SESSION)) { session_start(); }
if (isset($_SESSION["LoggedIn"]) && $_SESSION["LoggedIn"] && $_SESSION["Admin"]) {
    $Admin = filter_var($_GET["Admin"], FILTER_VALIDATE_BOOLEAN);
    $UserID = $_GET["UserID"];

    if ($Admin) {
        $sql = "SELECT AdminID FROM ADMIN WHERE AdminID=?";
        $prepare = mysqli_prepare($con, $sql);
        if ($prepare) {
            $prepare -> bind_param("s", $UserID);
            $prepare -> execute();
            $result = $prepare -> get_result();
            if (mysqli_num_rows($result) == 0) {
                $sql = "INSERT INTO ADMIN (AdminID) VALUES (?)";
                $prepare = mysqli_prepare($con, $sql);
                if ($prepare) {
                    $prepare -> bind_param("s", $UserID);
                    $prepare -> execute();
                }
            }
        }
        $prepare -> close();
    } else {
        $sql = "DELETE FROM ADMIN WHERE AdminID=?";
        $prepare = mysqli_prepare($con, $sql);
        if ($prepare) {
            $prepare -> bind_param("s", $UserID);
            $prepare -> execute();
        }
        $prepare -> close();
    }
    header("location: /admin/manage_users.php");
} else {
    // Someone not logged in is trying to update premium
    header("location: ../login.php");
}
?>
