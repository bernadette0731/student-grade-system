<?php
session_start();

// ===== Auth Guard =====
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$dataFile = "students.json";

function loadStudents($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function computeAverage($prelim, $midterm, $final) {
    return round(($prelim + $midterm + $final) / 3, 2);
}

function letterGrade($average) {
    if ($average >= 90) return "A";
    if ($average >= 85) return "B+";
    if ($average >= 80) return "B";
    if ($average >= 75) return "C+";
    if ($average >= 70) return "C";
    return "F";
}

$students = loadStudents($dataFile);

$students = array_map(function ($s) {
    $s["average"] = computeAverage($s["prelim"], $s["midterm"], $s["final"]);
    $s["letter"] = letterGrade($s["average"]);
    return $s;
}, $students);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Management - Student Grade Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="navbar">
        <h2>Student Grade Management System</h2>
        <div class="nav-links">
            <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION["user"]); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="grades.php" class="active">Grade Management</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1>Grade Management</h1>
                <p class="subtitle">View student grade records.</p>
            </div>

            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Section</th>
                        <th>Prelim</th>
                        <th>Midterm</th>
                        <th>Final</th>
                        <th>Average</th>
                        <th>Letter Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s["name"]); ?></td>
                            <td><?php echo htmlspecialchars($s["section"]); ?></td>
                            <td><?php echo htmlspecialchars($s["prelim"]); ?></td>
                            <td><?php echo htmlspecialchars($s["midterm"]); ?></td>
                            <td><?php echo htmlspecialchars($s["final"]); ?></td>
                            <td><?php echo htmlspecialchars($s["average"]); ?></td>
                            <td><?php echo htmlspecialchars($s["letter"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>