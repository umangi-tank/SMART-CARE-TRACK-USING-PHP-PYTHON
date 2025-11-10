<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

// Sample timetable: Replace with DB queries
$facultyTimetable = [
    'Monday' => [
        ['slot'=>'08:00 - 09:45', 'class'=>'Semester 1', 'subject'=>'Math', 'students'=>['John Doe','Aditi Mehta','Ravi Patel']],
        ['slot'=>'10:00 - 11:40', 'class'=>'Semester 2', 'subject'=>'Physics', 'students'=>['Student 1','Student 2','Student 3']],
        ['slot'=>'12:30 - 02:10', 'class'=>'Semester 3', 'subject'=>'Chemistry', 'students'=>['Student 4','Student 5']],
        ['slot'=>'02:10 - 03:50', 'class'=>'Semester 4', 'subject'=>'Biology', 'students'=>['Student 6','Student 7']],
        ['slot'=>'03:50 - 05:30', 'class'=>'Semester 5', 'subject'=>'CS', 'students'=>['Student 8','Student 9']],
    ],
    'Tuesday' => [
        ['slot'=>'08:00 - 09:45', 'class'=>'Semester 1', 'subject'=>'Math', 'students'=>['John Doe','Aditi Mehta','Ravi Patel']],
        ['slot'=>'10:00 - 11:40', 'class'=>'Semester 6', 'subject'=>'Physics', 'students'=>['Student 10','Student 11']],
        ['slot'=>'12:30 - 02:10', 'class'=>'Semester 7', 'subject'=>'Chemistry', 'students'=>['Student 12','Student 13']],
        ['slot'=>'02:10 - 03:50', 'class'=>'Semester 8', 'subject'=>'Biology', 'students'=>['Student 14','Student 15']],
        ['slot'=>'03:50 - 05:30', 'class'=>'Semester 2', 'subject'=>'CS', 'students'=>['Student 16','Student 17']],
    ],
    // Add Wednesday-Friday similarly
];

$slots = ['08:00 - 09:45','10:00 - 11:40','12:30 - 02:10','02:10 - 03:50','03:50 - 05:30'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendanceData = $_POST['attendance'] ?? [];
    $date = $_POST['date'] ?? date('Y-m-d');
    // TODO: Save attendance in DB
    echo "<script>alert('Attendance saved for $date'); window.location='faculty_attendance.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Weekly Timetable & Attendance</title>
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
.attendance-btn {background:#b71c1c; color:white;}
.attendance-btn:hover {background:#880e4f;}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <h2>Faculty Weekly Timetable</h2>
        <form method="post" action="">
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="form-control d-inline-block w-auto me-2">
            <button type="submit" class="logout-btn">Save Attendance</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Time Slot</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($slots as $slot): ?>
            <tr>
                <td><?php echo $slot; ?></td>
                <?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day):
                    if(isset($facultyTimetable[$day])){
                        $slotInfo = array_filter($facultyTimetable[$day], fn($s)=>$s['slot']==$slot);
                        $slotInfo = array_values($slotInfo);
                        if(count($slotInfo)>0){
                            $s = $slotInfo[0];
                            echo "<td><b>{$s['class']}</b><br>{$s['subject']}<br>
                                <button type='button' class='btn btn-sm btn-outline-primary mt-1' 
                                data-bs-toggle='modal' data-bs-target='#attendanceModal' 
                                data-class='{$s['class']}' data-students='".htmlspecialchars(json_encode($s['students']), ENT_QUOTES)."'>
                                Take Attendance
                                </button>
                            </td>";
                        } else echo "<td>-</td>";
                    } else echo "<td>-</td>";
                endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="attendanceModalLabel">Take Attendance</h5>
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
    students.forEach((s, i)=>{
        studentListDiv.innerHTML += `<div class="form-check">
            <input class="form-check-input" type="checkbox" name="attendance[${s}]" id="student_${i}" value="present" checked>
            <label class="form-check-label" for="student_${i}">${s}</label>
        </div>`;
    });
});
</script>

</body>
</html>
