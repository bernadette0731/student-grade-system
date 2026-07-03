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

$searchQuery = trim($_GET["q"] ?? "");

if ($searchQuery !== "") {
    $students = array_filter($students, function ($s) use ($searchQuery) {
        return stripos($s["name"], $searchQuery) !== false
            || stripos($s["section"], $searchQuery) !== false;
    });
}


$students = array_map(function ($s) {
    $s["average"] = computeAverage($s["prelim"], $s["midterm"], $s["final"]);
    $s["letter"] = letterGrade($s["average"]);
    return $s;
}, $students);

$sortBy = $_GET["sort"] ?? "name";
$order = $_GET["order"] ?? "asc";

usort($students, function ($a, $b) use ($sortBy, $order) {
    $valA = $a[$sortBy] ?? "";
    $valB = $b[$sortBy] ?? "";

    if (is_numeric($valA) && is_numeric($valB)) {
        $cmp = $valA <=> $valB;
    } else {
        $cmp = strcasecmp($valA, $valB);
    }

    return $order === "desc" ? -$cmp : $cmp;
});

function sortLink($column, $label, $currentSort, $currentOrder, $searchQuery) {
    $nextOrder = ($currentSort === $column && $currentOrder === "asc") ? "desc" : "asc";
    $arrow = "";
    if ($currentSort === $column) {
        $arrow = $currentOrder === "asc" ? " ▲" : " ▼";
    }
    $q = urlencode($searchQuery);
    return "<a href=\"grades.php?sort={$column}&order={$nextOrder}&q={$q}\">{$label}{$arrow}</a>";
}


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

            <form method="GET" action="grades.php" class="grade-form">
                <input type="text" name="q" placeholder="Search by name or section..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortBy); ?>">
                <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                <button type="submit" class="btn-primary">Search</button>
            </form>


            <table class="grades-table">
                <thead>
                   <tr>
                        <th><?php echo sortLink("name", "Name", $sortBy, $order, $searchQuery); ?></th>
                        <th><?php echo sortLink("section", "Section", $sortBy, $order, $searchQuery); ?></th>
                        <th><?php echo sortLink("prelim", "Prelim", $sortBy, $order, $searchQuery); ?></th>
                        <th><?php echo sortLink("midterm", "Midterm", $sortBy, $order, $searchQuery); ?></th>
                        <th><?php echo sortLink("final", "Final", $sortBy, $order, $searchQuery); ?></th>
                        <th><?php echo sortLink("average", "Average", $sortBy, $order, $searchQuery); ?></th>
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