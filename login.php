<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $usersJson = file_get_contents("users.json");
    $users = json_decode($usersJson, true);

    $authenticated = false;

    foreach ($users as $user) {
        if ($user["username"] == $username && $user["password"] == $password) {
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        $_SESSION["user"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Student Grade Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>

        <?php if ($error != "") { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST" action="login.php">
            <div>
                <label>Username</label>
                <input type="text" name="username">
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <button type="submit">Login</button>
        </form>

    </div>

    <script src="js/app.js"></script>
</body>
</html>