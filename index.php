<!DOCTYPE html>
<html>
<head>
    <title>Register Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <p><a href="login.php">Login</a></p>
    <center>
        <h2>Registration Form</h2>
    </center>
    <form action='register.inc.php' method='post'>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    
        <button type='submit' name="submit">Register</button>
    </form>

    <?php 
        if (isset($_GET["error"])) {
            if($_GET["error"] == "stmtFailed") {
                echo "<p>Something went wrong, please try again!</p>";
            }

            if($_GET["error"] == "usernameTaken") {
                echo "<p>User already exists!</p>";
            }

            if($_GET["error"] == "none") {
                header("location: ./login.php");
            }
        }
    ?>
</body>
</html>