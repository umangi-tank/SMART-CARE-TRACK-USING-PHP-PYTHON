<?php
session_start();
include 'db_connect.php';

// Check if session exists
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

while ($row = $res->fetch_assoc()) {
    $timetable[$row['day']][$row['time_slot']] = [
        'subject' => $row['subject'],
        'division' => $row['division']
    ];
}

// If attendance form is submitted
if (isset($_POST['save_attendance'])) {
    $subject = $_POST['subject'];
    $day = $_POST['day'];
    $slot = $_POST['slot'];
    $division = $_POST['division'];
    $attendance = $_POST['attendance'];
    $date = date('Y-m-d');

    foreach ($attendance as $roll_no => $status) {
        $stmt = $mysqli->prepare("INSERT INTO attendance (date, faculty_email, division, subject, day, time_slot, roll_no, status)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $date, $faculty_email, $division, $subject, $day, $slot, $roll_no, $status);
        $stmt->execute();
    }

    echo "<script>alert('Attendance saved successfully!');</script>";
}

// Load student list dynamically
$selected_day = $_POST['day'] ?? '';
$selected_slot = $_POST['slot'] ?? '';
$selected_subject = $_POST['subject'] ?? '';
$selected_division = $_POST['division'] ?? '';

$students = [];
if (!empty($selected_division)) {
    $stmt3 = $mysqli->prepare("SELECT * FROM students WHERE division = ?");
    $stmt3->bind_param("s", $selected_division);
    $stmt3->execute();
    $students = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Timetable & Attendance</title>
<style>
    body {
        font-family: "Poppins", sans-serif;
        background-color: #f5f7fa;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1100px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 30px;
    }
    h2 {
        text-align: center;
        color: #c00;
    }
    h4 {
        text-align: center;
        margin-top: -10px;
        color: #555;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #dcdcdc;
        padding: 12px;
        font-size: 15px;
    }
    th {
        background-color: #c00;
        color: white;
    }
    .break {
        background-color: #f9f9f9;
        font-weight: bold;
        color: #555;
    }
    .btn {
        margin-top: 5px;
        padding: 6px 10px;
        background-color: #c00;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 13px;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #a00;
    }
    .attendance-box {
        margin-top: 40px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .attendance-box table {
        margin-top: 10px;
    }
    button[type=submit] {
        background-color: #c00;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 15px;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Weekly Timetable</h2>
    <h4><?php echo strtoupper($faculty_name); ?></h4>

    <table>
        <tr>
            <th>Time Slot</th>
            <?php foreach ($days as $day) echo "<th>$day</th>"; ?>
        </tr>

        <?php foreach ($slots as $slot): ?>
            <?php if (strpos(strtolower($slot), 'break') !== false): ?>
                <tr class="break">
                    <td colspan="<?php echo count($days) + 1; ?>"><?php echo $slot; ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><strong><?php echo $slot; ?></strong></td>
                    <?php foreach ($days as $day): ?>
                        <td>
                            <?php
                            if (isset($timetable[$day][$slot])) {
                                $subject = strtoupper($timetable[$day][$slot]['subject']);
                                $division = $timetable[$day][$slot]['division'];
                                echo "<b>$subject</b><br><small>Div: $division</small><br>";
                                echo "<form method='POST' style='margin-top:5px;'>
                                        <input type='hidden' name='day' value='$day'>
                                        <input type='hidden' name='slot' value='$slot'>
                                        <input type='hidden' name='subject' value='$subject'>
                                        <input type='hidden' name='division' value='$division'>
                                        <button type='submit' class='btn'>Take Attendance</button>
                                      </form>";
                            } else {
                                echo "—";
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>

    <?php if (!empty($students)): ?>
        <div class="attendance-box">
            <h3 style="color:#c00;">Take Attendance for <?php echo "$selected_subject ($selected_division) - $selected_day [$selected_slot]"; ?></h3>
            <form method="POST">
                <input type="hidden" name="subject" value="<?php echo $selected_subject; ?>">
                <input type="hidden" name="day" value="<?php echo $selected_day; ?>">
                <input type="hidden" name="slot" value="<?php echo $selected_slot; ?>">
                <input type="hidden" name="division" value="<?php echo $selected_division; ?>">
                <table>
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo $s['roll_no']; ?></td>
                            <td><?php echo $s['full_name']; ?></td>
                            <td>
                                <input type="radio" name="attendance[<?php echo $s['roll_no']; ?>]" value="Present" required> P
                                <input type="radio" name="attendance[<?php echo $s['roll_no']; ?>]" value="Absent"> A
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <button type="submit" name="save_attendance">Save Attendance</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
