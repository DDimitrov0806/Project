<?php 
    require 'file.php';

    $fileData = $_POST['file']['fileData'];
    $fileName = $_POST['file']['fileName'];
    $fileHeader = $_POST['file']['fileHeader'];
    $exportType = $_POST['selectedValue'];

    $exportFileName = 'export_'.$fileName.'.'.$exportType;
    if($exportType === "xml") {
        $xml = new SimpleXMLElement('<root_element/>');

        foreach($fileData as $r) {
            $contact = $xml->addChild('element');
            foreach($fileHeader as $header) {
                $contact->addChild($header, $r[$header]);
            }
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        
        $dom->save($exportFileName);
    }
    else if($exportType === "json") {
        file_put_contents($exportFileName,json_encode($fileData));
    }
    else if($exportType === "csv") {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$exportFileName");
        
        $output = fopen("php://output", "wb");
        foreach($fileData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }
