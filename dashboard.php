<?php
session_start();

// Auth guard — matches the session key login.php actually sets
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
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

$students = loadStudents($dataFile);

// ===== Compute real dashboard stats =====
$totalStudents = count($students);

$uniqueSections = array_unique(array_map(function ($s) {
    return $s["section"] ?? "";
}, $students));
$totalSections = count(array_filter($uniqueSections));

$pendingGrades = 0;
$totalAverage = 0;

foreach ($students as $s) {
    $prelim = floatval($s["prelim"] ?? 0);
    $midterm = floatval($s["midterm"] ?? 0);
    $final = floatval($s["final"] ?? 0);

    if ($prelim === 0.0 || $midterm === 0.0 || $final === 0.0) {
        $pendingGrades++;
    }

    $totalAverage += computeAverage($prelim, $midterm, $final);
}

$overallAverage = $totalStudents > 0 ? round($totalAverage / $totalStudents, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Grade System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <h2>Student Grade System</h2>
        <div class="nav-links">
            <span class="welcome-text">Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <aside class="sidebar">
            <ul>
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="grades.php">📝 Grades</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p class="subtitle">Overview of student records and grades</p>
            </div>

            <div class="card-grid">
                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"></path>
                            <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5"></path>
                        </svg>
                    </div>
                    <h3><?php echo $totalStudents; ?></h3>
                    <p>Total Students</p>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <h3><?php echo $totalSections; ?></h3>
                    <p>Sections</p>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3><?php echo $pendingGrades; ?></h3>
                    <p>Incomplete Records</p>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"></path>
                            <path d="M18.7 8l-5.1 5.1-2.8-2.8L7 14"></path>
                        </svg>
                    </div>
                    <h3><?php echo $overallAverage; ?></h3>
                    <p>Overall Average</p>
                </div>
            </div>
        </main>
    </div>

    <script src="js/app.js"></script>
</body>
</html>