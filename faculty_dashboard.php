<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

// Sample data (replace with DB queries)
$leaveApplications = [
    ['id'=>1, 'student'=>'John Doe', 'type'=>'Sick Leave', 'from'=>'2025-09-01', 'to'=>'2025-09-02', 'status'=>'Approved'],
    ['id'=>2, 'student'=>'Aditi Mehta', 'type'=>'Casual Leave', 'from'=>'2025-09-05', 'to'=>'2025-09-05', 'status'=>'Pending'],
    ['id'=>3, 'student'=>'Ravi Patel', 'type'=>'Personal Leave', 'from'=>'2025-09-07', 'to'=>'2025-09-08', 'status'=>'Rejected']
];

$receivedComplaints = [
    ['id'=>1, 'title'=>'Classroom Projector Issue', 'date'=>'2025-09-03', 'status'=>'Resolved'],
    ['id'=>2, 'title'=>'Lab Equipment Problem', 'date'=>'2025-09-10', 'status'=>'Pending']
];

$announcements = [
    ['id'=>1, 'title'=>'Exam Timetable Released', 'date'=>'2025-09-15'],
    ['id'=>2, 'title'=>'Holiday on 20th Sep', 'date'=>'2025-09-18']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Dashboard</title>
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
.status-approved {
    color:green;
    font-weight:bold;
}
.status-rejected {
    color:red;
    font-weight:bold;
}
.status-pending {
    color:orange;
    font-weight:bold;
}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['faculty_email']); ?></h2>
            <p class="text-muted">Here’s an overview of your faculty activities</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Top 3 Boxes -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-calendar-check"></i>
                <h4><?php echo count($leaveApplications); ?></h4>
                <p>All Student Leave Requests</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-exclamation-circle"></i>
                <h4><?php echo count($receivedComplaints); ?></h4>
                <p>All Complaints</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-megaphone"></i>
                <h4><?php echo count($announcements); ?></h4>
                <p>New Announcements</p>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="table-section mt-4">
        <!-- Student Leave Requests Table -->
        <div class="row">
            <div class="col-md-12">
                <h4>Student Leave Requests</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($leaveApplications as $req): ?>
                        <tr>
                            <td><?php echo $req['id']; ?></td>
                            <td><?php echo $req['student']; ?></td>
                            <td><?php echo $req['type']; ?></td>
                            <td><?php echo $req['from']; ?></td>
                            <td><?php echo $req['to']; ?></td>
                            <td>
                                <?php 
                                    if ($req['status'] === 'Approved') {
                                        echo "<span class='status-approved'>Approved</span>";
                                    } elseif ($req['status'] === 'Rejected') {
                                        echo "<span class='status-rejected'>Rejected</span>";
                                    } else {
                                        echo "<span class='status-pending'>Pending</span>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Complaints Table -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Complaints Received</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($receivedComplaints as $comp): ?>
                        <tr>
                            <td><?php echo $comp['id']; ?></td>
                            <td><?php echo $comp['title']; ?></td>
                            <td><?php echo $comp['date']; ?></td>
                            <td>
                                <?php 
                                    if ($comp['status'] === 'Resolved') {
                                        echo "<span class='status-approved'>Resolved</span>";
                                    } else {
                                        echo "<span class='status-pending'>Pending</span>";
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
