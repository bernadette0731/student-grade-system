<?php
session_start();

$error = "";
$maxAttempts = 3;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Prefill username if "remember me" cookie exists
$rememberedUser = $_COOKIE['remembered_user'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $remember = isset($_POST['remember']);

    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $error = "Too many failed attempts. Please try again later.";
    } else {
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
            $_SESSION["user_id"] = $username;
            $_SESSION["username"] = $username;
            $_SESSION['login_attempts'] = 0;

            if ($remember) {
                setcookie("remembered_user", $username, time() + (86400 * 30), "/"); // 30 days
            } else {
                setcookie("remembered_user", "", time() - 3600, "/"); // clear if unchecked
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $remaining = $maxAttempts - $_SESSION['login_attempts'];
            $error = "Invalid username or password. $remaining attempt(s) remaining.";
        }
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
                <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($rememberedUser); ?>">
            </div>
            <div>
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" required>
                    <span class="toggle-password" id="togglePassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
            </div>
            <span class="error" id="clientError" style="display:none;"></span>

            <label class="remember-me">
                <input type="checkbox" name="remember" id="remember" <?php echo $rememberedUser ? 'checked' : ''; ?>>
                Remember me
            </label>

            <button type="submit" id="loginBtn">Login</button>
        </form>

    </div>

    <script src="js/app.js"></script>
</body>
</html>