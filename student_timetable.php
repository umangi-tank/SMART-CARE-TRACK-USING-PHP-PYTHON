<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Normalize DB handle: prefer $conn, else use $mysqli
if (!isset($conn) && isset($mysqli)) {
    $conn = $mysqli;
}

// If still not set, error out
if (!isset($conn)) {
    die("Database connection not found. Make sure db_connect.php defines \$conn or \$mysqli.");
}

$user_email = $_SESSION['user_email'];

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$student) {
    die("Student record not found.");
}

// Safely pick a display name using fallbacks so undefined-key warnings don't occur
$student_name = $student['full_name'] ?? $student['name'] ?? $student['student_name'] ?? '';
$semester = $student['semester'] ?? $student['sem'] ?? '';
$division = $student['division'] ?? $student['division_name'] ?? '';

// Ensure non-null strings for htmlspecialchars
$student_name = (string)$student_name;
$semester = (string)$semester;
$division = (string)$division;

// Time slots and days
$slots = [
    '8:05-8:55',
    '8:55-9:45',
    'Tea Break',
    '10:00-10:50',
    '10:50-11:40',
    'Lunch Break',
    '12:30-1:20',
    '1:20-2:10'
];

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

// Fetch timetable rows for this student's semester & division
$cells = []; // $cells[time_slot][day] = ['subject'=>..., 'faculty'=>...]

// Try both possible column names in timetable WHERE clause: semester or sem
// First try with 'semester' column; if that fails, fallback to 'sem'.
$query_try = [
    "SELECT time_slot, day, subject, faculty FROM timetable WHERE semester = ? AND division = ?",
    "SELECT time_slot, day, subject, faculty FROM timetable WHERE sem = ? AND division = ?"
];

$gotRows = false;
foreach ($query_try as $sql) {
    $q = $conn->prepare($sql);
    if (!$q) {
        // prepare failed: try next fallback
        continue;
    }
    $q->bind_param("ss", $semester, $division);
    $q->execute();
    $res = $q->get_result();
    if ($res && $res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            $ts = $r['time_slot'] ?? '';
            $day = $r['day'] ?? '';
            $subject = $r['subject'] ?? '';
            // note: timetable stores faculty name in 'faculty' column (per your setup)
            $faculty = $r['faculty'] ??  $r['faculty_name'] ?? '';

            if ($ts !== '' && $day !== '') {
                $cells[$ts][$day] = [
                    'subject' => $subject,
                    'faculty' => $faculty
                ];
            }
        }
        $gotRows = true;
    }
    $q->close();
    // if we already found rows, we can break; else continue to next fallback
    if ($gotRows) break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Weekly Time Table - Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; padding:0; }
.sidebar { width:240px; position:fixed; top:0; left:0; min-height:100vh; background:#fff; border-right:1px solid #ddd; padding-top:20px; }
.sidebar a { display:block; padding:12px 20px; color:#333; text-decoration:none; border-radius:6px; margin:5px 10px; }
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }
.content { margin-left:240px; padding:20px; }
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}
.table-bordered { border: 2px solid #b71c1c; text-align: center; }
.table-bordered th, .table-bordered td { vertical-align: middle; }
.break-row { background-color: #f2f2f2; font-weight: bold; }
.subject { font-weight: 600; }
.faculty { font-size: 0.9em; color: #555; }
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Weekly Time Table</h2>
            <p class="text-muted">
                Student: <strong><?php echo htmlspecialchars($student_name); ?></strong> |
                Semester: <strong><?php echo htmlspecialchars($semester); ?></strong> |
                Division: <strong><?php echo htmlspecialchars($division); ?></strong>
            </p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <?php if (empty($cells)): ?>
        <div class="alert alert-warning">No timetable found for Semester <?php echo htmlspecialchars($semester); ?>, Division <?php echo htmlspecialchars($division); ?>.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Time Slot</th>
                    <?php foreach ($days as $d): ?>
                        <th><?php echo htmlspecialchars($d); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slots as $slot): ?>
                    <?php if (stripos($slot, 'Break') !== false): ?>
                        <tr class="break-row">
                            <td><?php echo htmlspecialchars($slot); ?></td>
                            <td colspan="<?php echo count($days); ?>"><?php echo htmlspecialchars($slot); ?></td>
                        </tr>
                        <?php continue; ?>
                    <?php endif; ?>

                    <tr>
                        <td><strong><?php echo htmlspecialchars($slot); ?></strong></td>
                        <?php foreach ($days as $day):
                            $cell = $cells[$slot][$day] ?? null; ?>
                            <td>
                                <?php if ($cell): ?>
                                    <div class="subject"><?php echo nl2br(htmlspecialchars((string)$cell['subject'])); ?></div>
                                    <div class="faculty"><?php echo nl2br(htmlspecialchars((string)$cell['faculty'])); ?></div>
                                <?php else: ?>
                                    — 
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
