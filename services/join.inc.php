<?php

if (!isset($_POST['submit'])) {
    header("location: ../parser.php");
    exit();
}

$fileName1 = $_POST["joinOption1"];
$fileName2 = $_POST["joinOption2"];

$fileColumn1 = $_POST["joinColumn1"];
$fileColumn2 = $_POST["joinColumn2"];

require_once "./db-connect.inc.php";
require_once "./file.inc.php";
require_once "../file.php";

$file1 = getFileByFilename($conn, $fileName1);

if ($file1 === false) {
    header("location: ../parser.php?error=fileNotFound");
    exit();
}

$file2 = getFileByFilename($conn, $fileName2);

if ($file2 === false) {
    header("location: ../parser.php?error=fileNotFound");
    exit();
}

$isKey1Present = array_search(strtolower($search), array_map('strtolower', $file1->getFileHeader()));
$isKey2Present = array_search(strtolower($search), array_map('strtolower', $file2->getFileHeader()));

if(!$isKey1Present || !$isKey2Present) {
    header("location: ../parser.php?error=invalidColumn");
    exit();
}

$joinedHeader = array_unique(array_merge($file1->getFileHeader(), $file2->getFileHeader()));
$joinedData = array();

$fileData1 = $file1->getFileData();
$fileData2 = $file2->getFileData();

foreach ($fileData1 as $row1) {
    $lowerRow1 = array_change_key_case($row1, CASE_LOWER);
    $joinValue1 = strtolower($lowerRow1[strtolower($fileColumn1)]);

    foreach ($fileData2 as $row2) {
        $lowerRow2 = array_change_key_case($row2, CASE_LOWER);
        $joinValue2 = strtolower($lowerRow2[strtolower($fileColumn2)]);

        if ($joinValue1 === $joinValue2) {
            $joinedRow = array_merge($row1, $row2);
            $joinedData[] = $joinedRow;
        }
    }
}

$fileName = $fileName1 . "_" . strtolower($fileColumn1) . "_" . $fileName2 . "_" . strtolower($fileColumn2);

insertFile($conn, $joinedData, $fileName, $joinedHeader);

header("location: ../parser.php");
exit();
