<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Login Form</h2>
        <form action="login.inc.php" method='post' class="colm-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        
            <button type='submit' name="submit">Login</button>
        </form>
        
        <p>Don't have an account? <a href="index.php">Register here</a></p>
    </div>

    <?php 
        if (isset($_GET["error"])) {
            if($_GET["error"] == "userNotExist") {
                echo "<p>User does not exist!</p>";
            }

            if($_GET["error"] == "wrondPassword") {
                echo "<p>The entered password is incorrect</p>";
            }

            if($_GET["error"] == "none") {
                header("location: ./upload.php");
            }
        }
    ?>
</body>
</html>
