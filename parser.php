<!DOCTYPE html>
<html>

<head>
    <title>Vizualize</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>

<body>
    <?php
    require 'file.php';

    $fileInfos = array();
    
    if (isset($_FILES['filename'])) {

        for ($i = 0; $i < count($_FILES['filename']['error']); $i++) {
            $contentType = $_FILES['filename']['type'][$i];
            $fileContent = file_get_contents($_FILES['filename']['tmp_name'][$i]);
            $filename = $_FILES['filename']['name'][$i];

            //Check if the file content is read successfully
            if (!$fileContent) {
                echo "<p>There was an error while reading the contents of the file</p>";
                return;
            }

            // Determine the file type based on the extension
            if ($contentType === 'application/vnd.ms-excel') {
            } elseif ($contentType === 'application/xml' || $contentType === 'text/xml') {
                $xml = simplexml_load_string($fileContent);
                $headers = array();
                $xmlArray = [];

                foreach ($xml as $row) {
                    #echo $row;
                    $xmlArray[] = (array)$row;
                }

                foreach ($xmlArray[0] as $key => $value) {
                    $headers[] = $key;
                }

                $fileInfo = new FileInfo($xmlArray, $filename, $headers);
                array_push($fileInfos, $fileInfo);
            } elseif ($contentType === 'text/csv') {
                $lines = explode("\n", $fileContent);
                $headers = str_getcsv(array_shift($lines));
                $data = array();
                foreach ($lines as $line) {

                    $row = array();

                    foreach (str_getcsv($line) as $key => $field)
                        $row[$headers[$key]] = $field;

                    $row = array_filter($row);

                    $data[] = $row;
                }

                $fileInfo = new FileInfo($data, $filename, $headers);
                array_push($fileInfos, $fileInfo);
            } elseif ($contentType === 'application/json') {
                $jsonObject = json_decode($fileContent, true);
                $headers = array();

                foreach ($jsonObject[0] as $key => $value) {
                    $headers[] = $key;
                }

                $fileInfo = new FileInfo($jsonObject, $filename, $headers);
                array_push($fileInfos, $fileInfo);
            } else {
                $fileType = 'Unknown';
            }
        }
    }

    foreach ($fileInfos as $file) {
        $file->printTable();
    }

    ?>

    <script>
        function unionTables(table1Id, table2Id) {
            var table1 = document.getElementById(table1Id);
            var table2 = document.getElementById(table2Id);


            if (!table1 || !table2) {
                console.error("One or both of the tables not found. Check table IDs.");
                return;
            }

            var rowsTable1 = table1.getElementsByTagName("tr");
            var rowsTable2 = table2.getElementsByTagName("tr");
            var rowsTable1Length = rowsTable1.length;
            var rowsTable2Length = rowsTable2.length;

            var unionTable = document.createElement("table");
            unionTable.id = "unionTable";

            unionTable.appendChild(rowsTable1[0].cloneNode(true));

            for (var i = 1; i < rowsTable1Length; i++) {
                unionTable.appendChild(rowsTable1[i].cloneNode(true));
            }
            for (var j = 1; j < rowsTable2Length; j++) {
                unionTable.appendChild(rowsTable2[j].cloneNode(true));
            }

            document.body.appendChild(unionTable);
        }

        window.addEventListener("load", function() {
            document.getElementById("UnionTables").onclick = function() {
                unionTables('fileName', 'table_filename2');
            };
        });

    </script>
    <script>
        function sortTable(n, tableId) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById(tableId);
            switching = true;
            dir = "asc";
            /* Make a loop that will continue until
            no switching has been done: */
            while (switching) {
                switching = false;
                rows = table.rows;
                /* Loop through all table rows (except the
                first, which contains table headers): */
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                var thElements = table.getElementsByTagName("TH");

                /* Add event listeners to table header elements */
                for (var j = 0; j < thElements.length; j++) {
                    thElements[j].addEventListener("click", function() {
                        // Remove active class from all header elements
                        for (var k = 0; k < thElements.length; k++) {
                            thElements[k].classList.remove("active");
                        }

                        // Add active class to the clicked header element
                        this.classList.add("active");
                    });
                }
                if (shouldSwitch) {
                    /* If a switch has been marked, make the switch
                    and mark that a switch has been done: */
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    // Each time a switch is done, increase this count by 1:
                    switchcount++;
                    for (var l = 0; l < thElements.length; l++) {
                        thElements[l].classList.remove("asc", "desc");
                    }
                    thElements[n].classList.add(dir);
                } else {
                    /* If no switching has been done AND the direction is "asc",
                    set the direction to "desc" and run the while loop again. */
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }

        function search(inputId, jsonFile) {
            filter = document.getElementById(inputId).value;

            $.ajax({
                    type: 'POST',
                    url: 'search.php',
                    data: { 'file': $jsonFile , 'filter': filter },
                    dataType: 'json'
                }).done(function(res) {
                    <?php array_push($fileInfos,)?>
                });
/*
            console.log(inputId);
            console.log(tableId);

            var input, filter, table, tr, i, txtValue;
            input = document.getElementById(inputId);
            filter = input.value.toUpperCase();
            table = document.getElementById(tableId);
            tr = table.getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td");
                tr[i].style.display = "none";
                for (var cell = 0; cell < td.length; cell++) {
                    if (td[cell].innerHTML.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        continue;
                    }
                }
            }
  */
        }

        var joinedTablesArray = [];

        function joinTables(table1Id, table2Id, columnName) {
            var table1 = document.getElementById(table1Id);
            var table2 = document.getElementById(table2Id);
            if (!table1 || !table2) {
                console.error("One or both of the tables not found. Check table IDs.");
                return;
            }
            var headersTable1 = table1.getElementsByTagName("th");
            var headersTable2 = table2.getElementsByTagName("th");

            var columnIndex1 = -1;
            var columnIndex2 = -1;

            for (var i = 0; i < headersTable1.length; i++) {
                if (headersTable1[i].innerText.toLowerCase() === columnName.toLowerCase()) {
                    columnIndex1 = i;
                    break;
                }
            }

            for (var i = 0; i < headersTable2.length; i++) {
                if (headersTable2[i].innerText.toLowerCase() === columnName.toLowerCase()) {
                    columnIndex2 = i;
                    break;
                }
            }

            if (columnIndex1 === -1 || columnIndex2 === -1) {
                console.error("Column name not found in one or both of the tables.");
                return;
            }

            var joinedTable = document.createElement("table");
            joinedTable.id = "joinedTable";
            joinedTable.classList.add("joined-table");

            var newRow = document.createElement("tr");
            for (var i = 0; i < headersTable1.length; i++) {
                newRow.appendChild(headersTable1[i].cloneNode(true));
            }

            for (var i = 0; i < headersTable2.length; i++) {
                if (i !== columnIndex2) newRow.appendChild(headersTable2[i].cloneNode(true));
            }
            joinedTable.appendChild(newRow);

            var rowsTable1 = table1.getElementsByTagName("tr");
            var rowsTable2 = table2.getElementsByTagName("tr");

            for (var i = 1; i < rowsTable1.length; i++) {
                for (var j = 1; j < rowsTable2.length; j++) {
                    if (rowsTable1[i].cells[columnIndex1] && rowsTable2[j].cells[columnIndex2] &&
                        rowsTable1[i].cells[columnIndex1].innerText === rowsTable2[j].cells[columnIndex2].innerText) {

                        newRow = document.createElement("tr");

                        for (var k = 0; k < rowsTable1[i].cells.length; k++) {
                            newRow.appendChild(rowsTable1[i].cells[k].cloneNode(true));
                        }

                        for (var k = 0; k < rowsTable2[j].cells.length; k++) {
                            if (k !== columnIndex2) newRow.appendChild(rowsTable2[j].cells[k].cloneNode(true));
                        }

                        joinedTable.appendChild(newRow);
                    }
                }
            }
            joinedTablesArray.push(joinedTable);
            document.body.appendChild(joinedTable);
        }


        document.addEventListener("DOMContentLoaded", function() {
            var joinButton = document.getElementById("joinButton");
            if (joinButton) {
                joinButton.onclick = function() {
                    joinTables(document.getElementById('joinDropdownMenu1').value, document.getElementById('joinDropdownMenu2').value, document.getElementById('joinColumnName').value);
                };
            } else {
                console.error("Join button not found. Check the HTML.");
            }
        });


        function exportTableToJson(tableId) {
            var table = document.getElementById(tableId);
            if (!table) {
                console.error("Table not found. Check table ID.");
                return;
            }

            var headers = table.getElementsByTagName("th");
            var rows = table.getElementsByTagName("tr");
            var jsonArray = [];

            for (var i = 1; i < rows.length; i++) {
                var rowData = {};
                for (var j = 0; j < rows[i].cells.length; j++) {
                    rowData[headers[j].innerText] = rows[i].cells[j].innerText;
                }
                jsonArray.push(rowData);
            }

            return JSON.stringify(jsonArray, null, 2);
        }
    </script>
    
    <p>Union:</p>

    <select id="dropdownMenu1">
        <?php foreach ($fileInfos as $fileInfo) : ?>
            <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
        <?php endforeach; ?>
    </select>
    <select id="dropdownMenu2">
        <?php foreach ($fileInfos as $fileInfo) : ?>
            <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
        <?php endforeach; ?>
    </select>

    <button id="unionButton" onclick="unionTables(document.getElementById('dropdownMenu1').value,
     document.getElementById('dropdownMenu2').value)">Union</button>

    <p>Join:</p>

    <select id="joinDropdownMenu1">
        <?php foreach ($fileInfos as $fileInfo) : ?>
            <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
        <?php endforeach; ?>
    </select>
    <select id="joinDropdownMenu2">
        <?php foreach ($fileInfos as $fileInfo) : ?>
            <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" id="joinColumnName" placeholder="Column Name" required>

    <button id="joinButton" onclick="joinTables(document.getElementById('joinDropdownMenu1').value, document.getElementById('joinDropdownMenu2').value, document.getElementById('joinColumnName').value)">Join</button>

</body>

</html>