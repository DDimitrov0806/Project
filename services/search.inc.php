<?php 

    if(!isset($_POST['search'])) {
        header("location: ../parser.php");
        exit();
    }

    require_once "../file.php";
    require_once "./db-connect.inc.php";
    require_once "./file.inc.php";

    $fileName = $_POST['fileName'];
    $filter = strtolower($_POST['filter']);
    $filteredData = array();

    $file = getFileByFilename($conn,$fileName);
    if($file === false) {
        header("location: ../parser.php?error=fileNotFound");
        exit();
    }
    $fileData = $file->getFileData();

    foreach($fileData as $rowData) {
        foreach($rowData as $data) {
            if(str_contains(strtolower($data),$filter)){
                array_push($filteredData,$rowData);
                break;
            }
        }
    }

    $filterFileName = $filter . "_" . $fileName;
    insertFile($conn, $filteredData,$filterFileName,$file->getFileHeader());

    header("location: ../parser.php");
    exit();
