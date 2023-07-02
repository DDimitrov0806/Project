<?php 

    require_once '../file.php';
    require_once './file.inc.php';
    require_once './db-connect.inc.php';

    $fileName = $_POST['fileName'];
    $exportType = $_POST['exportType'];

    $file = getFileByFilename($conn,$fileName);

    $exportFileName = 'export_'.$fileName.'.'.$exportType;
    if($exportType === "xml") {
        $xml = new SimpleXMLElement('<root_element/>');

        foreach($file->getFileData() as $r) {
            $contact = $xml->addChild('element');
            foreach($file->getFileHeader() as $header) {
                $contact->addChild($header, $r[$header]);
            }
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        $fileData = $dom->saveXML();
        
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename=' . $exportFileName);

        $fh = fopen('php://output', 'w');

        fwrite($fh,$fileData);

        fclose($fh);
    }
    else if($exportType === "json") {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=' . $exportFileName);
        
        $fh = fopen('php://output', 'w');

        fwrite($fh,json_encode($file->getFileData(),JSON_PRETTY_PRINT));

        fclose($fh);
    }
    else if($exportType === "csv") {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$exportFileName");

        $fh = fopen('php://output', 'w');

        fputcsv($fh, $file->getFileHeader());
        
        foreach($file->getFileData() as $row) {
            fputcsv($fh, $row);
        }        

        fclose($fh);
    }
