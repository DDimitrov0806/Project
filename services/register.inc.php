<?php 

if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    require_once "db-connect.inc.php";
    require_once "authentication-helper.inc.php";

    if(checkUserExists($conn,$username) !== false) {
        header("location: ../index.php?error=usernameTaken");
        exit();
    }

    createUser($conn,$username,$password);
} 
else {
    header("location: ../index.php");
    exit();
}