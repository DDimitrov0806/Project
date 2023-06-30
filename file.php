<?php

class FileInfo {
    private $fileData;
    private $fileName;
    private $fileHeader;

    public function __construct($fileData, $fileName, $fileHeader) {
        $this->fileData = $fileData;
        $this->fileName = $fileName;
        $this->fileHeader = $fileHeader;
    }

    public function getFileData() {
        return $this->fileData;
    }

    public function setFileData($fileData) {
        $this->fileData = $fileData;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getFileHeader() {
        return $this->fileHeader;
    }

    public function setFileHeader($fileHeader) {
        $this->fileHeader = $fileHeader;
    }

    public function printTable() {
        echo "<div>";
        echo "Table from file: " . $this -> fileName;
        echo "</div>";

        $tableId=$this-> fileName;
        $inputId = $tableId."input";
        $selectedValue = "";
        $jsonFile = json_encode($this);

        echo "<input type='text' id='$inputId' onkeyup=\"search('$inputId','$tableId')\" placeholder='Search'>";

        echo "<select class=\"form-control event\" name='event_name' id='event_name'>
                <option value=\"<?php $selectedValue = 'xml'?> echo 'xml' \">XML</option>
                <option value=\"<?php $selectedValue = 'json'?> echo 'json' \">JSON</option>
                <option value=\"<?php $selectedValue = 'csv' echo 'csv'?> \">CSV</option>
            </select>";
        echo "<button onclick='exportTable()'> Export </button>";
        echo "<script>
                function exportTable(){
                    $.ajax({
                        type: 'POST',
                        url: 'export.php',
                        data: '{file:  $jsonFile }',
                        dataType: 'json'
                    })
                }
            </script>";
        
        echo "";

        echo "<table id='$tableId'>";
            if (!empty($this -> fileData)) {
                $counter = 0;
            
                echo "<tr>";
                foreach ($this -> fileHeader as $header) {
                    echo "<th onclick=\"sortTable($counter,'$tableId')\">$header</th>";
                    $counter++;
                }
                echo "</tr>";

                foreach ($this -> fileData as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
            }
        echo "</table>";
    }

    /*
    private function exportTable($exportType){
        $exportFileName = 'export_'.$this->fileName;
        if($exportType === "xml") {
            $xml = new SimpleXMLElement('<root_element/>');

            foreach($this -> fileName as $r) {
                $contact = $xml->addChild('element');
                foreach($this -> fileHeader as $header) {
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
            file_put_contents($exportFileName,json_encode($this->fileData));
        }
        else if($exportType === "csv") {
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=$exportFileName");
            
            $output = fopen("php://output", "wb");
            foreach($this->fileData as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }
    }*/
}
