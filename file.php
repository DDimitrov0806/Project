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

        echo "<form method='post' action='services/search.inc.php'>";
        echo "<input name='filter' type='text' placeholder='Search'></input>";
        echo "<input type='hidden' name='fileName' value=$this->fileName></input>";
        echo "<input type=\"submit\" name=\"search\" class=\"btn btn-success\" /> ";
        echo "</form>";

        echo "<form method=\"post\" action=\"services/export.inc.php\" align=\"center\">";
        echo "<select name='exportType'>
                <option value=\"xml\" selected>XML</option>
                <option value=\"json\">JSON</option>
                <option value=\"csv\">CSV</option>
            </select>";
        echo "<input type='hidden' name='fileName' value=$this->fileName></input>";
        echo "<input type=\"submit\" name=\"export\" class=\"btn btn-success\" /> 
            </form>";

        echo "<form method='post' action='services/sort.inc.php'>
            <select name='sortColumn'>";
            
            foreach ($this -> fileHeader as $header) {
                echo "<option value='$header'>$header</option>";
            }
        echo "</select>";
        echo "<select name='order'>
                <option value=\"asc\" selected>Ascending</option>
                <option value=\"desc\">Descending</option>
            </select>
            <input type='hidden' name='fileName' value=$this->fileName></input>
            <input type=\"submit\" name=\"sort\" class=\"btn btn-success\" />";    
        echo "
            </form>";

        echo "<table>";
            if (!empty($this -> fileData)) {
                echo "<tr>";
                foreach ($this -> fileHeader as $header) {
                    echo "<th>$header</th>";
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