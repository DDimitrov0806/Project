<?php

if(!isset($_POST['sort'])) {
    header("location: ../parser.php");
    exit();
}


$sortColumn = $_POST['sortColumn'];
$fileName = $_POST['fileName'];
$order = $_POST['order'];

require_once './db-connect.inc.php';
require_once './file.inc.php';
require_once '../file.php';

$file = getFileByFilename($conn,$fileName);

$fileData = $file->getFileData();

$column = array_column($fileData, $sortColumn);

array_multisort($column,$order == 'desc' ? SORT_DESC : SORT_ASC, $fileData);

$sortFileName = $order."_".$sortColumn."_".$fileName;

insertFile($conn,$fileData,$sortFileName,$file->getFileHeader());
header("location: ../parser.php");
exit();