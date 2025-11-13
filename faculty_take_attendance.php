<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

include 'db_connect.php';

$faculty_email = $_SESSION['faculty_email'];

// Step 1: Get faculty full name
$stmt = $mysqli->prepare("SELECT full_name FROM faculty WHERE email = ?");
$stmt->bind_param("s", $faculty_email);
$stmt->execute();
$res = $stmt->get_result();
$faculty = $res->fetch_assoc();
$faculty_name = $faculty['full_name'] ?? '';
$stmt->close();

// Step 2: Get timetable entries assigned to this faculty
$q = $mysqli->prepare("SELECT semester, division, time_slot, day, subject FROM timetable WHERE faculty = ? ORDER BY FIELD(day,'Monday','Tuesday','Wednesday','Thursday','Friday'), time_slot");
$q->bind_param("s", $faculty_name);
$q->execute();
$result = $q->get_result();

// Organize timetable into array for easy display
$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
$slots = ['08:00-09:45','10:00-11:40','12:30-02:10','02:10-03:50','03:50-05:30'];
$cells = [];

while($row = $result->fetch_assoc()){
    $ts = $row['time_slot'];
    $day = $row['day'];
    $cells[$ts][$day] = [
        'semester' => $row['semester'],
        'division' => $row['division'],
        'subject' => $row['subject'],
        'faculty' => $faculty_name
    ];
}
$q->close();

// Step 3: Get students list for each semester+division
$students_by_class = [];
$stu_res = $mysqli->query("SELECT full_name, semester, division FROM students");
while($s = $stu_res->fetch_assoc()){
    $key = $s['semester'].'-'.$s['division'];
    $students_by_class[$key][] = $s['full_name'];
}

// Step 4: Handle attendance save (if DB table exists)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name'])){
    $attendance_data = $_POST['attendance'] ?? [];
    $class_name = $_POST['class_name'];
    $date = $_POST['date'] ?? date('Y-m-d');
    
    // Loop through attendance data and save into DB
    // Example query (table 'attendance' should exist):
    /*
    foreach($attendance_data as $student_name => $status){
        $stmt = $conn->prepare("INSERT INTO attendance (student_name,class_name,date,status,faculty) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$student_name,$class_name,$date,$status,$faculty_name);
        $stmt->execute();
        $stmt->close();
    }
    */
    echo "<script>alert('Attendance for $class_name saved successfully'); window.location='faculty_take_attendance.php';</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Timetable & Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; padding:0; }
.sidebar { width:240px; position:fixed; top:0; left:0; min-height:100vh; background:#fff; border-right:1px solid #ddd; padding-top:20px; }
.sidebar a { display:block; padding:12px 20px; color:#333; text-decoration:none; border-radius:6px; margin:5px 10px; }
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }
.content { margin-left:240px; padding:20px; }
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}
.table-bordered { border:2px solid #b71c1c; text-align:center; }
.table-bordered th, .table-bordered td { vertical-align: middle; }
.break-row {background:#f2f2f2; font-weight:bold;}
.subject { font-weight: 600; }
.teacher { font-size: 0.9em; color: #555; }
.attendance-btn {background:#b71c1c; color:white;}
.attendance-btn:hover {background:#880e4f;}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <h2>Weekly Timetable & Attendance</h2>
        <form method="post">
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="form-control d-inline-block w-auto me-2">
            <button type="submit" class="logout-btn">Save Attendance</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Time Slot</th>
                <?php foreach($days as $d) echo "<th>$d</th>"; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($slots as $slot): ?>
            <tr>
                <td><strong><?php echo $slot; ?></strong></td>
                <?php foreach($days as $day): 
                    if(isset($cells[$slot][$day])):
                        $c = $cells[$slot][$day];
                        $class_key = $c['semester'].'-'.$c['division'];
                        $students = $students_by_class[$class_key] ?? [];
                        ?>
                        <td>
                            <b><?php echo $c['semester'].'-'.$c['division']; ?></b><br>
                            <?php echo $c['subject']; ?><br>
                            <small><?php echo $c['faculty']; ?></small><br>
                            <?php if(!empty($students)): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" 
                                data-bs-toggle="modal" data-bs-target="#attendanceModal" 
                                data-class="<?php echo $class_key; ?>" 
                                data-students='<?php echo htmlspecialchars(json_encode($students), ENT_QUOTES); ?>'>
                                Take Attendance
                            </button>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                        <td>-</td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Take Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>">
            <input type="hidden" name="class_name" id="class_name">
            <div id="student_list"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn attendance-btn">Save Attendance</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const attendanceModal = document.getElementById('attendanceModal');
attendanceModal.addEventListener('show.bs.modal', event=>{
    const button = event.relatedTarget;
    const className = button.getAttribute('data-class');
    const students = JSON.parse(button.getAttribute('data-students'));
    document.getElementById('class_name').value = className;
    const studentListDiv = document.getElementById('student_list');
    studentListDiv.innerHTML = '';
    students.forEach((s,i)=>{
        studentListDiv.innerHTML += `<div class="form-check">
            <input class="form-check-input" type="radio" name="attendance[${s}]" id="present_${i}" value="Present" checked>
            <label class="form-check-label" for="present_${i}">${s} - Present</label>
            <input class="form-check-input ms-3" type="radio" name="attendance[${s}]" id="absent_${i}" value="Absent">
            <label class="form-check-label" for="absent_${i}">Absent</label>
        </div>`;
    });
});
</script>

</body>
</html>
