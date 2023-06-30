<!DOCTYPE html>
<html>

<head>
    <title>Drag and Drop File Upload</title>
    <link rel="stylesheet" href="styleIndex.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
</head>

<body>
    <form action="parser.php" method="post" enctype="multipart/form-data">
        <input type="file" id="myFile" name="filename[]" multiple>
        <input type="submit">
    </form>
</body>

</html>