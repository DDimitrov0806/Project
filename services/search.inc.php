<?php 
    include "file.php";

    $fileData = $_POST['file']['fileData'];
    $fileName = $_POST['file']['fileName'];
    $fileHeader = $_POST['file']['fileHeader'];
    $filter = strtolower($_POST['filter']);
    $filteredData = array();

    foreach($fileData as $rowData) {
        foreach($rowData as $data) {
            if(str_contains(strtolower($rowData),$filter)){
                array_push($filteredData,$rowData);
                break;
            }
        }
    }

    require_once "db-connect.inc.php";
    require_once "file.inc.php";

    //$fileInfo = new FileInfo($$filteredData, $fileName, $fileHeader);
    return $fileInfo;
?>