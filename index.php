<?php
session_start();

// if (isset($_SESSION['user_id'])) {
//     header("Location: dashboard.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grade System</title>
    <link rel="stylesheet"
</head>
<body>
    <div class="landing-container">
        <div class="landing-card">
            <div class="login-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"></path>
                    <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5"></path>
                </svg>
            </div>
            <h1>Student Grade System</h1>
            <p>A simple way to record, track, and manage student academic performance.</p>
            <a href="login.php" class="btn-primary">Get Started</a>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>