<!DOCTYPE html>
<html>

<head>
    <title>Vizualize</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>

<body>
    <?php 
        if (isset($_GET["error"])) {
            if($_GET["error"] == "stmtFailed") {
                echo "<p>Something went wrong, please try again!</p>";
            }

            if($_GET["error"] == "fileNotFound") {
                echo "<p>File is not found</p>";
            }

            if($_GET["error"] == "none") {
                header("location: ./parser.php");
            }
        }
    ?>


    <?php
    require_once 'file.php';
    
    if (!isset($_POST['submit'])) {
        header("location: ./upload.php");
    }
    
    $fileInfos = array();

    require_once "db-connect.inc.php";
    session_start();
    
    $sql = "SELECT * FROM files WHERE userId = ?;";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ./upload.php?error=stmtFailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $_SESSION['userId']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_assoc($result)) {
        array_push($fileInfos, new FileInfo(json_decode($row['fileData']),$row['fileName'],json_decode($row['fileHeaders'])));
    }

    if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'][0] != "") {
        $importFiles = array();
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
            if ($contentType === 'application/xml' || $contentType === 'text/xml') {
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
                array_push($importFiles, $fileInfo);
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
                array_push($importFiles, $fileInfo);
            } elseif ($contentType === 'application/json') {
                $jsonObject = json_decode($fileContent, true);
                $headers = array();

                foreach ($jsonObject[0] as $key => $value) {
                    $headers[] = $key;
                }

                $fileInfo = new FileInfo($jsonObject, $filename, $headers);
                array_push($importFiles, $fileInfo);
            } else {
                $fileType = 'Unknown';
            }
        }

        foreach ($importFiles as $file) {
            $extensionPos = strpos($file->getFileName(),'.xml');
            $extensionPos = $extensionPos ? $extensionPos : strpos($file->getFileName(),'.json');
            $extensionPos = $extensionPos ? $extensionPos : strpos($file->getFileName(),'.csv');
            
            $cleanFileName = substr($file->getFileName(),0,$extensionPos-strlen($file->getFileName()));
            
            insertFile($conn,$file->getFileData(), $cleanFileName,$file->getFileHeader());
            array_push($fileInfos, $file);
        }
    }

    foreach ($fileInfos as $file) {
        $file->printTable();
    }

    ?>

    <script>
        function search(inputId, jsonFile) {
            filter = document.getElementById(inputId).value;

            $.ajax({
                type: 'POST',
                url: 'search.php',
                data: {
                    'file': $jsonFile,
                    'filter': filter
                },
                dataType: 'json'
            });
        }
    </script>

    <p>Union:</p>

    <form action="union.php" method="post">
        <select name="joinOption1">
            <?php foreach ($fileInfos as $fileInfo) : ?>
                <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="joinOption2">
            <?php foreach ($fileInfos as $fileInfo) : ?>
                <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
            <?php endforeach; ?>
        </select>

        <input type='submit' name="submit">
    </form>

    <p>Join:</p>

    <select id="joinDropdownMenu1">
        <?php foreach ($fileInfos as $fileInfo) : ?>
            <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
            <select id="joinOptionDropdownMenu1">
                <?php foreach($fileInfo->getFileHeader() as $fileHeader) : ?>
                    <option value="<?= $fileHeader ?>"><?= $fileHeader ?></option>
                <?php endforeach; ?>
            </select>
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