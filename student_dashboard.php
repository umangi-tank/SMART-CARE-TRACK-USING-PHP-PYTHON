<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample data arrays (replace with DB queries)
$leaveRequests = [
    ['id'=>1,'type'=>'Sick Leave','from'=>'2025-09-01','to'=>'2025-09-02','status'=>'Approved'],
    ['id'=>2,'type'=>'Casual Leave','from'=>'2025-09-05','to'=>'2025-09-05','status'=>'Pending']
];
$complaints = [
    ['id'=>1,'title'=>'Hostel Issue','date'=>'2025-09-03','status'=>'Resolved'],
    ['id'=>2,'title'=>'Library Complaint','date'=>'2025-09-10','status'=>'Pending']
];
// Attendance percentages
$attendance = [
    'average'=>88,
    'last_month'=>90,
    'current_month'=>92
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard</title>
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
.dashboard-header {
    margin-bottom:30px; 
    display:flex; 
    justify-content:space-between; 
    align-items:center;
}

/* Top 4 boxes */
.card-box {
    border:1px solid #b71c1c; 
    border-radius:8px; 
    padding:20px; 
    background:#fff; 
    cursor:pointer; 
    transition:0.3s; 
    height:150px;
}
.card-box:hover {background:#f2f2f2;}
.card-box i {
    font-size:32px; 
    margin-bottom:10px; 
    color:#b71c1c;
}
.card-box h4 {
    font-size:22px; 
    font-weight:bold; 
    margin-bottom:5px;
}
.card-box p {
    font-size:14px; 
    margin:0;
}

/* Attendance sub-boxes below tables */
.attendance-sub {
    border:1px solid #b71c1c; 
    border-radius:6px; 
    padding:15px; 
    margin:5px; 
    text-align:center; 
    background:#fff;
    width:30%; 
    display:inline-block;
}

.table-section {margin-top:40px;}
table th, table td {vertical-align:middle;}

.logout-btn {
    background:#b71c1c; 
    color:#fff; 
    border:none; 
    padding:6px 12px; 
    border-radius:4px;
}
.logout-btn:hover {background:#880e4f;}
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Here’s an overview of your activities</p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Top 4 Boxes -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card-box text-center">
                <i class="bi bi-calendar-check"></i>
                <h4><?php echo count($leaveRequests); ?></h4>
                <p>Total Leave Requests</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <i class="bi bi-exclamation-circle"></i>
                <h4><?php echo count($complaints); ?></h4>
                <p>Total Complaints</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <i class="bi bi-speaker"></i>
                <h4>2</h4>
                <p>New Announcements</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-box text-center">
                <i class="bi bi-check2-circle"></i>
                <h4><?php echo $attendance['current_month']; ?>%</h4>
                <p>Current Month Attendance</p>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="table-section">
        <div class="row">
            <!-- Leave Requests Table -->
            <div class="col-md-6">
                <h4>Leave Requests</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Id</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($leaveRequests as $leave): ?>
                        <tr>
                            <td><?php echo $leave['id']; ?></td>
                            <td><?php echo $leave['type']; ?></td>
                            <td><?php echo $leave['from']; ?></td>
                            <td><?php echo $leave['to']; ?></td>
                            <td><?php echo $leave['status']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Complaints Table -->
            <div class="col-md-6">
                <h4>Complaints</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($complaints as $comp): ?>
                        <tr>
                            <td><?php echo $comp['id']; ?></td>
                            <td><?php echo $comp['title']; ?></td>
                            <td><?php echo $comp['date']; ?></td>
                            <td><?php echo $comp['status']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance Sub-Boxes Below Tables -->
    <div class="text-center mt-4">
        <div class="attendance-sub">
            <strong>Average Attendance</strong>
            <p><?php echo $attendance['average']; ?>%</p>
        </div>
        <div class="attendance-sub">
            <strong>Last Month</strong>
            <p><?php echo $attendance['last_month']; ?>%</p>
        </div>
        <div class="attendance-sub">
            <strong>Current Month</strong>
            <p><?php echo $attendance['current_month']; ?>%</p>
        </div>
    </div>

</div>
</body>
</html>
