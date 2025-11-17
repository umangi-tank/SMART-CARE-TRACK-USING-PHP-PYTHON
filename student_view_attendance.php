<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; // make sure this connects $mysqli

$student_email = $_SESSION['user_email'];

// ---------------------- FETCH STUDENT INFO ----------------------
$stmt = $mysqli->prepare("SELECT full_name, enrollment_no, school, department, program, semester, division, admission_year, mobile, father_mobile,  roll_no 
                          FROM students 
                          WHERE email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();


// ---------------------- FETCH ATTENDANCE FOR THIS STUDENT ----------------------
$attendance = [];
$stmt_att = $mysqli->prepare("SELECT date, day, faculty_email, class_name, student_name, roll_no, division, status 
                              FROM attendance 
                              WHERE roll_no = ? 
                              ORDER BY date ASC");
$stmt_att->bind_param("s", $student['roll_no']);
$stmt_att->execute();
$result_att = $stmt_att->get_result();
while ($row = $result_att->fetch_assoc()) {
    $attendance[] = $row;
}
$stmt_att->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Attendance</title>
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
.table th, .table td { vertical-align: middle; }
.card { background:#fff; border-radius:10px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-bottom:20px;}
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Your Attendance Records</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Student Info -->
    <div class="card">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
                <p><strong>College:</strong> <?php echo htmlspecialchars($student['school']); ?></p>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
                <p><strong>Division:</strong> <?php echo htmlspecialchars($student['division']); ?></p>
                <p><strong>Student Mobile No:</strong> <?php echo htmlspecialchars($student['mobile']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no']); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($student['semester']); ?></p>
                <p><strong>Batch:</strong> <?php echo htmlspecialchars($student['admission_year']); ?></p>
                <p><strong>Father Mobile No:</strong> <?php echo htmlspecialchars($student['father_mobile']); ?></p>
                <p><strong>Roll No:</strong> <?php echo htmlspecialchars($student['roll_no']); ?></p>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Sr. No.</th>
                    <th>Updated From</th>
                    <th>Day</th>
                    <th>Faculty</th>
                    <th>Class</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Division</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($attendance) > 0): ?>
                    <?php foreach($attendance as $index => $att): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($att['date']); ?></td>
                            <td><?php echo htmlspecialchars($att['day']); ?></td>
                            <td><?php echo htmlspecialchars($att['faculty_email']); ?></td>
                            <td><?php echo htmlspecialchars($att['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($att['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($att['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($att['division']); ?></td>
                            <td><?php echo htmlspecialchars($att['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">No attendance records found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
