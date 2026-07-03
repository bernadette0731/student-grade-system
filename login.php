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
        $_SESSION["username"] = $username;
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
        <div class="login-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"></path>
                <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5"></path>
            </svg>
        </div>
        <h2 class="loginText">Login</h2>
        <p class="subtitle">Sign in to access your dashboard</p>

        <?php if ($error != "") { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST" action="login.php" class="login-form">
            <div>
                <label>Username</label>
                <input type="text" name="username" id="username">
            </div>
            <div>
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password">
                    <span class="toggle-password" id="togglePassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
            </div>
            <button type="submit" id="loginBtn">Login</button>
        </form>

    </div>

    <script src="js/app.js"></script>
</body>
</html>