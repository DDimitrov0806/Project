<!DOCTYPE html>
<html>

<head>
    <title>Drag and Drop File Upload</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
</head>

<body>
    <form class='upload' action="parser.php" method="post" enctype="multipart/form-data">
        <input class="upload-area" type="file" id="myFile" name="filename[]" multiple>
        <input class="upload-button" type="submit" name="submit">
    </form>

    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "fileNotFound") {
            echo "<p class='error'>Cannot find file</p>";
        }

        if ($_GET["error"] == "stmtFailed") {
            echo "<p class='error'>There was a problem. Please try again!</p>";
        }
    }
    ?>
</body>

</html>