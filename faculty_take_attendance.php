<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

$faculty_email = $_SESSION['faculty_email'];

// Fetch faculty name
$sql = "SELECT full_name FROM faculty WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $faculty_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $faculty_name = $row['full_name'];
} else {
    die("Faculty not found.");
}

// Fetch timetable for this faculty
$sql2 = "SELECT * FROM timetable WHERE faculty = ?";
$stmt2 = $mysqli->prepare($sql2);
$stmt2->bind_param("s", $faculty_name);
$stmt2->execute();
$res = $stmt2->get_result();

$timetable = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
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

while ($r = $res->fetch_assoc()) {
    // ensure keys exist
    $dayKey = $r['day'];
    $slotKey = $r['time_slot'];
    $timetable[$dayKey][$slotKey] = [
        'subject' => $r['subject'],
        'division' => $r['division']
    ];
}

// Handle attendance save
if (isset($_POST['save_attendance'])) {
    // Use posted date if provided (form includes date), otherwise today's date
    $date = $_POST['date'] ?? date('Y-m-d');
    $day  = $_POST['day']  ?? date('l'); // fallback to weekday name
    $subject = $_POST['subject'] ?? '';
    $slot = $_POST['slot'] ?? '';
    $division = $_POST['division'] ?? '';
    $attendance = $_POST['attendance'] ?? [];

    // Prepared insert statement matching the SQL table you requested earlier
    $insert_sql = "INSERT INTO attendance (date, day, faculty_email, class_name, student_name, roll_no, division, status)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $mysqli->prepare($insert_sql);

    if (!$insert_stmt) {
        die("Prepare failed: " . htmlspecialchars($mysqli->error));
    }

    foreach ($attendance as $roll_no => $status) {
        // fetch student name from students table
        $student_name = 'Unknown';
        $student_q = $mysqli->prepare("SELECT full_name FROM students WHERE roll_no = ?");
        if ($student_q) {
            $student_q->bind_param("i", $roll_no);
            $student_q->execute();
            $sr = $student_q->get_result()->fetch_assoc();
            if ($sr && !empty($sr['full_name'])) {
                $student_name = $sr['full_name'];
            }
            $student_q->close();
        }

        // bind and execute insert
        $insert_stmt->bind_param(
            "sssssiis",
            $date,
            $day,
            $faculty_email,
            $subject,       // class_name
            $student_name,
            $roll_no,
            $division,
            $status
        );
        $insert_stmt->execute();
    }

    $insert_stmt->close();

    echo "<script>alert('Attendance saved successfully!');</script>";
}

// Student list if "Take Attendance" clicked (single page)
$selected_day = $_POST['day'] ?? '';
$selected_slot = $_POST['slot'] ?? '';
$selected_subject = $_POST['subject'] ?? '';
$selected_division = $_POST['division'] ?? '';
$students = [];
if (!empty($selected_division)) {
    $stmt3 = $mysqli->prepare("SELECT roll_no, full_name FROM students WHERE division = ? ORDER BY roll_no ASC");
    $stmt3->bind_param("s", $selected_division);
    $stmt3->execute();
    $students = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Faculty Timetable & Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
}
.content {
    margin-left:230px;
    padding:20px;
}
h2 {
    color:#b71c1c;
    font-weight:bold;
}
.table th {
    background-color:#b71c1c;
    color:white;
}
.table td {
    vertical-align:middle;
}
.btn-red {
    background-color:#b71c1c;
    color:white;
    border:none;
    border-radius:4px;
    padding:6px 12px;
}
.btn-red:hover { background-color:#880e4f; }
.break-row { background:#f0f0f0; font-weight:bold; }
.attendance-box { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-top:30px; }
.logout-btn { background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; }
.logout-btn:hover { background:#880e4f; }
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Weekly Timetable & Attendance</h2>
            <p class="text-muted mb-0">Faculty: <strong><?php echo htmlspecialchars(strtoupper($faculty_name)); ?></strong></p>
        </div>
        <form method="post" action="index.php">
           
        </form>
    </div>

    <!-- Timetable -->
    <div class="card p-3 shadow-sm">
        <table class="table table-bordered text-center align-middle mb-0">
            <thead>
                <tr>
                    <th>Time Slot</th>
                    <?php foreach ($days as $day): ?>
                        <th><?php echo htmlspecialchars($day); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slots as $slot): ?>
                    <?php if (stripos($slot, 'break') !== false): ?>
                        <tr class="break-row">
                            <td colspan="<?php echo count($days) + 1; ?>"><?php echo htmlspecialchars($slot); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($slot); ?></strong></td>
                            <?php foreach ($days as $day): ?>
                                <td>
                                    <?php
                                    if (isset($timetable[$day][$slot])) {
                                        $subject = $timetable[$day][$slot]['subject'];
                                        $division = $timetable[$day][$slot]['division'];
                                        echo '<b>' . htmlspecialchars(strtoupper($subject)) . '</b><br>';
                                        echo '<small>Div: ' . htmlspecialchars($division) . '</small><br>';
                                        // form posts back to same page and loads student list
                                        echo "<form method='POST' class='mt-2'>";
                                        echo "<input type='hidden' name='day' value=\"" . htmlspecialchars($day) . "\">";
                                        echo "<input type='hidden' name='slot' value=\"" . htmlspecialchars($slot) . "\">";
                                        echo "<input type='hidden' name='subject' value=\"" . htmlspecialchars($subject) . "\">";
                                        echo "<input type='hidden' name='division' value=\"" . htmlspecialchars($division) . "\">";
                                        echo "<button type='submit' class='btn btn-red btn-sm'>Take Attendance</button>";
                                        echo "</form>";
                                    } else {
                                        echo "—";
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Attendance Box -->
    <?php if (!empty($students)): ?>
        <div class="attendance-box mt-4">
            <h4 class="mb-3 text-danger">
                Attendance for <?php echo htmlspecialchars("$selected_subject ($selected_division) - $selected_day [$selected_slot]"); ?>
            </h4>

            <p><strong>Date:</strong> <?php echo date('d M Y'); ?></p>

            <form method="POST">
                <input type="hidden" name="subject" value="<?php echo htmlspecialchars($selected_subject); ?>">
                <input type="hidden" name="day" value="<?php echo htmlspecialchars($selected_day); ?>">
                <input type="hidden" name="slot" value="<?php echo htmlspecialchars($selected_slot); ?>">
                <input type="hidden" name="division" value="<?php echo htmlspecialchars($selected_division); ?>">
                <input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>">

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['roll_no']); ?></td>
                                <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                                <td>
                                    <input type="radio" name="attendance[<?php echo htmlspecialchars($s['roll_no']); ?>]" value="Present" required> Present
                                    <input type="radio" name="attendance[<?php echo htmlspecialchars($s['roll_no']); ?>]" value="Absent"> Absent
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <button type="submit" name="save_attendance" class="btn-red mt-2">Save Attendance</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
