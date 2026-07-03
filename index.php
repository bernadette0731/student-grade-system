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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="landing-container">
        <div class="landing-card">
            <div class="landing-icon">🎓</div>
            <h1>Student Grade System</h1>
            <p>A simple way to record, track, and manage student academic performance.</p>
            <a href="login.php" class="btn-primary">Get Started</a>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>