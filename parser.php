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
                echo "<p class='error'>Something went wrong, please try again!</p>";
            }

            if($_GET["error"] == "fileExists") {
                echo "<p class='error'>The file already exists</p>";
            }

            if($_GET["error"] == "fileNotFound") {
                echo "<p class='error'>File is not found</p>";
            }

            if($_GET["error"] == "fileHeadersNotEqual") {
                echo "<p class='error'>The two tables don't have the same headers</p>";
            }

            if($_GET["error"] == "invalidColumn") {
                echo "<p class='error'>The provided column is invalid!</p>";
            }

            if($_GET["error"] == "none") {
                header("location: ./parser.php");
            }
        }
    ?>


    <?php
    require_once 'file.php';
    
    $fileInfos = array();

    require_once "services/db-connect.inc.php";
    require_once "services/file.inc.php";
    
    $fileInfos = getFilesForUser($conn);

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

    <p>Union:</p>

    <form action="services/union.inc.php" method="post">
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
    <form action='services/join.inc.php' method='post'>
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
        <input type="text" name="joinColumn1" placeholder="Table 1 Column" required>
        <input type="text" name="joinColumn2" placeholder="Table 2 Column" required>
        <input type="submit" name="submit">
    </form>

    <p>Intersect:</p>
    <form action="services/intersect.inc.php" method="post">
        <select name="intersectOption1">
            <?php foreach ($fileInfos as $fileInfo) : ?>
                <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="intersectOption2">
            <?php foreach ($fileInfos as $fileInfo) : ?>
                <option value="<?= $fileInfo->getFileName(); ?>"><?= $fileInfo->getFileName(); ?></option>
            <?php endforeach; ?>
        </select>

        <input type='submit' name="submit">
    </form>

</body>

</html>