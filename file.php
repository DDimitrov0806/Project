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
        $inputId = "input".$tableId;
        $exportTypeId = "export".$tableId;
        $temp = get_object_vars($this);
        $jsonFile = json_encode(get_object_vars($this));

        echo "<input type='text' id='$inputId' onkeyup=\"search('$inputId','$tableId')\" placeholder='Search'>";

        echo "<select id='$exportTypeId'>
                <option value=\"xml\" selected>XML</option>
                <option value=\"json\">JSON</option>
                <option value=\"csv\">CSV</option>
            </select>";
        echo "<button onclick='exportTable()' type='submit'> Export </button>";
        echo "<script>
                function exportTable(){
                    var selected = document.getElementById('$exportTypeId').value;
                    console.log(selected);
                    $.ajax({
                        type: 'POST',
                        url: 'export.php',
                        data: { 'file': $jsonFile , 'selectedValue': selected },
                        dataType: 'json'
                    })
                }
            </script>";

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
} ?>