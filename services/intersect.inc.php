<?php

if (isset($_POST['submit'])) {
    require_once "../file.php";

    $fileName1 = $_POST['intersectOption1'];
    $fileName2 = $_POST['intersectOption2'];

    require_once "db-connect.inc.php";
    require_once "file.inc.php";

    $file1 = getFileByFilename($conn, $fileName1);

    if ($file1 === false) {
        header("location: ../upload.php?error=fileNotFound");
        exit();
    }

    $file2 = getFileByFilename($conn, $fileName2);

    if ($file2 === false) {
        header("location: ../upload.php?error=fileNotFound");
        exit();
    }


    if ($file1->getFileHeader() !== $file2->getFileHeader()) {
        header("location: ../parser.php?error=fileHeadersNotEqual");
        exit();
    }

    $fileData1 = $file1->getFileData();
    $fileData2 = $file2->getFileData();

    $fileName = "intersect_" .$fileName1 . '_' . $fileName2;
    $intersectData = array();

    foreach($fileData1 as $row1) {
        foreach($fileData2 as $row2) {
            if ($row1 === $row2) {
                array_push($intersectData,$row1);
            }
        }
    }

    //$intersecttData = array_intersect($fileData1, $fileData2);

    insertFile($conn, $intersectData, $fileName, $file1->getFileHeader());
    header("location: ../parser.php");
}
