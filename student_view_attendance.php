<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample student data
$student = [
    'name' => 'TANK UMANGI ASHOKBHAI',
    'enroll' => '23SOECE13023',
    'college' => 'SOE',
    'department' => 'Computer Engineering',
    'course' => 'UCE',
    'semester' => 'Sem-VII',
    'division' => 'A',
    'batch' => '7CEA',
    'student_mobile' => '9173914174',
    'father_mobile' => '7623045838',
    'term_start' => '24/06/2025',
    'term_end' => '14/11/2025'
];

// Sample month-wise attendance data
$attendance = [
    ['month'=>'June - 2025','total_lectures'=>2,'absent'=>2,'present'=>0,'percentage'=>0.00],
    ['month'=>'July - 2025','total_lectures'=>44,'absent'=>29,'present'=>15,'percentage'=>34.09],
    ['month'=>'August - 2025','total_lectures'=>35,'absent'=>29,'present'=>6,'percentage'=>17.14],
    ['month'=>'September - 2025','total_lectures'=>30,'absent'=>12,'present'=>18,'percentage'=>60.00],
    ['month'=>'October - 2025','total_lectures'=>20,'absent'=>5,'present'=>15,'percentage'=>75.00]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Month-wise Attendance</title>
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
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Month-wise Average Attendance List</p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Student Info -->
    <div class="card mb-4 p-3">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?php echo $student['name']; ?></p>
                <p><strong>College:</strong> <?php echo $student['college']; ?></p>
                <p><strong>Course:</strong> <?php echo $student['course']; ?></p>
                <p><strong>Division:</strong> <?php echo $student['division']; ?></p>
                <p><strong>Student Mobile No:</strong> <?php echo $student['student_mobile']; ?></p>
                <p><strong>Term Date:</strong> <?php echo $student['term_start'] . ' - ' . $student['term_end']; ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Enrollment No:</strong> <?php echo $student['enroll']; ?></p>
                <p><strong>Department:</strong> <?php echo $student['department']; ?></p>
                <p><strong>Semester:</strong> <?php echo $student['semester']; ?></p>
                <p><strong>Batch:</strong> <?php echo $student['batch']; ?></p>
                <p><strong>Father Mobile No:</strong> <?php echo $student['father_mobile']; ?></p>
            </div>
        </div>

        <!-- Month Selection -->
        <div class="mt-3">
            <label for="monthSelect"><strong>Month:</strong></label>
            <select id="monthSelect" class="form-select" style="width:200px;">
                <?php foreach($attendance as $att): ?>
                    <option value="<?php echo $att['month']; ?>"><?php echo $att['month']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card p-3">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Sr. No.</th>
                    <th>Month</th>
                    <th>Total Lecture</th>
                    <th>Absent Lecture</th>
                    <th>Present Lecture</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($attendance as $index => $att): ?>
                    <tr>
                        <td><?php echo $index+1; ?></td>
                        <td><?php echo $att['month']; ?></td>
                        <td><?php echo $att['total_lectures']; ?></td>
                        <td><?php echo $att['absent']; ?></td>
                        <td><?php echo $att['present']; ?></td>
                        <td><?php echo $att['percentage']; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
