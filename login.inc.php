<?php 

if(isset($_POST["submit"])) {
    require_once "db-connect.inc.php";
    require_once "authentication-helper.inc.php";

    $username = $_POST["username"];
    $password = $_POST["password"];

    $existingUser = checkUserExists($conn,$username);

    if($existingUser == false) {
        header("location: ./login.php?error=userNotExist");
        exit();
    }

    $hashedPassword = $existingUser["password"];

    $checkPassword = password_verify($password, $hashedPassword);

    if($checkPassword === false) {
        header("location: ./login.php?error=wrondPassword");
        exit();
    }
    else if($checkPassword === true ) {
        session_start();

        $_SESSION['userId'] = $existingUser['userId'];

        header("location: ./login.php?error=none");
        exit();
    }
}
else {
    header("location: ./login.php");
}