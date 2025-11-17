<?php
session_start();
include 'db_connect.php'; // make sure this connects $mysqli

if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

$faculty_email = $_SESSION['faculty_email'];

// ---------------------- FETCH LEAVE REQUESTS ----------------------
$leaveApplications = [];
$sql_leave = "SELECT id, student_name, student_email, leave_type, from_date, to_date, status 
              FROM leave_requests 
              ORDER BY created_at DESC";
$result_leave = $mysqli->query($sql_leave);
if ($result_leave && $result_leave->num_rows > 0) {
    while ($row = $result_leave->fetch_assoc()) {
        $leaveApplications[] = $row;
    }
}

// ---------------------- FETCH COMPLAINTS ----------------------
$receivedComplaints = [];
$sql_complaints = "SELECT id, student_name, student_email, complaint_type, complaint_date, status 
                   FROM complaints 
                   ORDER BY created_at DESC";
$result_complaints = $mysqli->query($sql_complaints);
if ($result_complaints && $result_complaints->num_rows > 0) {
    while ($row = $result_complaints->fetch_assoc()) {
        $receivedComplaints[] = $row;
    }
}

// ---------------------- FETCH ANNOUNCEMENTS ----------------------
$announcements = [];
$sql_ann = "SELECT id, title, message, date 
            FROM announcements 
            ORDER BY created_at DESC";
$result_ann = $mysqli->query($sql_ann);
if ($result_ann && $result_ann->num_rows > 0) {
    while ($row = $result_ann->fetch_assoc()) {
        $announcements[] = $row;
    }
}
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
.message-cell {
    max-width:400px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($faculty_email); ?></h2>
            <p class="text-muted">Here’s an overview of your faculty activities</p>
        </div>
        <form method="post" action="faculty_login.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Top 3 Summary Boxes -->
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
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($leaveApplications) > 0): ?>
                            <?php foreach ($leaveApplications as $req): ?>
                                <tr>
                                    <td><?php echo $req['id']; ?></td>
                                    <td><?php echo htmlspecialchars($req['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($req['student_email']); ?></td>
                                    <td><?php echo htmlspecialchars($req['leave_type']); ?></td>
                                    <td><?php echo htmlspecialchars($req['from_date']); ?></td>
                                    <td><?php echo htmlspecialchars($req['to_date']); ?></td>
                                    <td>
                                        <?php 
                                            if ($req['status'] === 'Approved') echo "<span class='status-approved'>Approved</span>";
                                            elseif ($req['status'] === 'Rejected') echo "<span class='status-rejected'>Rejected</span>";
                                            else echo "<span class='status-pending'>Pending</span>";
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">No leave requests found</td></tr>
                        <?php endif; ?>
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
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Complaint Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($receivedComplaints) > 0): ?>
                            <?php foreach ($receivedComplaints as $comp): ?>
                                <tr>
                                    <td><?php echo $comp['id']; ?></td>
                                    <td><?php echo htmlspecialchars($comp['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($comp['student_email']); ?></td>
                                    <td><?php echo htmlspecialchars($comp['complaint_type']); ?></td>
                                    <td><?php echo htmlspecialchars($comp['complaint_date']); ?></td>
                                    <td>
                                        <?php 
                                            if ($comp['status'] === 'Resolved') echo "<span class='status-approved'>Resolved</span>";
                                            elseif ($comp['status'] === 'Rejected') echo "<span class='status-rejected'>Rejected</span>";
                                            else echo "<span class='status-pending'>Pending</span>";
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No complaints found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h4>Announcements</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($announcements) > 0): ?>
                            <?php foreach ($announcements as $a): ?>
                                <tr>
                                    <td><?php echo $a['id']; ?></td>
                                    <td><?php echo htmlspecialchars($a['title']); ?></td>
                                    <td><?php echo htmlspecialchars($a['date']); ?></td>
                                    <td class="message-cell"><?php echo htmlspecialchars($a['message']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No announcements found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
