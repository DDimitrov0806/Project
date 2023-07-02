<?php

session_start();

function insertFile($conn, $fileData, $fileName, $fileHeader)
{
    $sql = "INSERT INTO files(userId,fileData,fileName,fileHeaders) VALUES (?,?,?,?);";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ./parser.php?error=stmtFailed");
        exit();
    }

    $fileData = json_encode($fileData);
    $fileHeader = json_encode($fileHeader);

    mysqli_stmt_bind_param($stmt, "isss", $_SESSION['userId'], $fileData, $fileName, $fileHeader);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function getFileByFilename($conn, $fileName)
{
    $sql = "SELECT * FROM files WHERE fileName = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ./parser.php?error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $fileName);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return new FileInfo(json_decode($row['fileData']), $row['fileName'], json_decode($row['fileHeaders']));
    } else {
        return false;
    }
}
