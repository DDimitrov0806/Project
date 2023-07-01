<?php

if(isset($_POST['submit'])) {
    require_once "file.php";

    $fileName1 = $_POST['joinOption1'];
    $fileName2 = $_POST['joinOption2'];
    
    require_once "db-connect.inc.php";

    $sql = "SELECT * FROM files WHERE fileName = ? OR fileName = ? ;";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ./upload.php?error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $fileName1, $fileName2);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $files = array();

    while($row = mysqli_fetch_assoc($result)) {
        array_push($files,new FileInfo(json_decode($row['fileData']),$row['fileName'],json_decode($row['fileHeaders'])));
    }

    $fileData1=$files[0]->getFileData();
    $fileData2=$files[1]->getFileData();

    $unionData = array_merge($fileData1,$fileData2);

    $sql = "INSERT INTO files(userId,fileData,fileName,fileHeaders) VALUES (?,?,?,?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ./upload.php?error=stmtFailed");
        exit();
    }

    session_start();
    $fileData = json_encode($unionData);
    $fileName = $files[0]->getFileName().'_'.$files[1]->getFileName();
    $fileHeader = json_encode($files[0]->getFileHeader());

    mysqli_stmt_bind_param($stmt, "isss", $_SESSION['userId'], $fileData, $fileName, $fileHeader);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location: ./parser.php");
}