<?php 
require 'file.php';

    $file = json_decode($_POST['file']);

    $exportFileName = 'export_'.$file->fileName;
    if($exportType === "xml") {
        $xml = new SimpleXMLElement('<root_element/>');

        foreach($file -> fileName as $r) {
            $contact = $xml->addChild('element');
            foreach($file -> fileHeader as $header) {
                $contact->addAttribute($header, $r[$header]);
            }
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        
        $dom->save($exportFileName);
    }
    else if($exportType === "json") {
        file_put_contents($exportFileName,json_encode($file->fileData));
    }
    else if($exportType === "csv") {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$exportFileName");
        
        $output = fopen("php://output", "wb");
        foreach($file->fileData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }
