<?php
session_start();

// ===== Auth Guard =====
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$dataFile = "students.json";
$message = "";

function loadStudents($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function saveStudents($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
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

// Add new student record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add") {
    $name = trim($_POST["name"] ?? "");
    $section = trim($_POST["section"] ?? "");
    $prelim = floatval($_POST["prelim"] ?? 0);
    $midterm = floatval($_POST["midterm"] ?? 0);
    $final = floatval($_POST["final"] ?? 0);

    if ($name !== "" && $section !== "") {
        $newId = 1;
        foreach ($students as $s) {
            if ($s["id"] >= $newId) {
                $newId = $s["id"] + 1;
            }
        }

        $students[] = [
            "id" => $newId,
            "name" => $name,
            "section" => $section,
            "prelim" => $prelim,
            "midterm" => $midterm,
            "final" => $final
        ];

        saveStudents($dataFile, $students);
        $message = "Student record added successfully.";
    } else {
        $message = "Name and section are required.";
    }
}

// Delete student record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $deleteId = intval($_POST["id"] ?? 0);
    $students = array_values(array_filter($students, function ($s) use ($deleteId) {
        return $s["id"] != $deleteId;
    }));
    saveStudents($dataFile, $students);
    $message = "Student record deleted.";
}

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
                <p class="subtitle">View, search, sort, and manage student grade records.</p>
            </div>

            <?php if ($message !== ""): ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

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
                        <th>Action</th>
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
                            <td>
                                <form method="POST" action="grades.php" onsubmit="return confirm('Delete this record?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo intval($s["id"]); ?>">
                                    <button type="submit" class="btn-logout">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

              <div class="card" id="addRecordCard">
                <h3>Add New Grade Record</h3>
                <form method="POST" action="grades.php" class="grade-form">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="name" placeholder="Student Name" required>
                    <input type="text" name="section" placeholder="Section" required>
                    <input type="number" step="0.01" name="prelim" placeholder="Prelim" min="0" max="100" required>
                    <input type="number" step="0.01" name="midterm" placeholder="Midterm" min="0" max="100" required>
                    <input type="number" step="0.01" name="final" placeholder="Final" min="0" max="100" required>
                    <button type="submit" class="btn-primary">Add Student</button>
                </form>
            </div>

        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>