<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; // MySQLi connection
$student_email = $_SESSION['user_email'];

// ---------------------- Fetch Leave Requests ----------------------
$leaveRequests = [];
$stmt = $mysqli->prepare("SELECT id, leave_type, from_date, to_date, status 
                          FROM leave_requests 
                          WHERE student_email = ? 
                          ORDER BY from_date DESC");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $leaveRequests[] = $row;
}
$stmt->close();

// ---------------------- Fetch Complaints ----------------------
$complaints = [];
$stmt = $mysqli->prepare("SELECT id, complaint_type, complaint_date, status 
                          FROM complaints 
                          WHERE student_email = ? 
                          ORDER BY complaint_date DESC");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
$stmt->close();

// ---------------------- Fetch Announcements ----------------------
$announcements = [];
$stmt = $mysqli->prepare("SELECT id, title, message, date 
                          FROM announcements 
                          ORDER BY created_at DESC 
                          LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}
$stmt->close();
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

/* Top Boxes */
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
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Top Boxes -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-calendar-check"></i>
                <h4><?php echo count($leaveRequests); ?></h4>
                <p>Total Leave Requests</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-exclamation-circle"></i>
                <h4><?php echo count($complaints); ?></h4>
                <p>Total Complaints</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box text-center">
                <i class="bi bi-speaker"></i>
                <h4><?php echo count($announcements); ?></h4>
                <p>New Announcements</p>
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
                        <?php if(count($leaveRequests) > 0): ?>
                            <?php foreach($leaveRequests as $leave): ?>
                                <tr>
                                    <td><?php echo $leave['id']; ?></td>
                                    <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                    <td><?php echo $leave['from_date']; ?></td>
                                    <td><?php echo $leave['to_date']; ?></td>
                                    <td><?php echo $leave['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">No leave requests found</td></tr>
                        <?php endif; ?>
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
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($complaints) > 0): ?>
                            <?php foreach($complaints as $comp): ?>
                                <tr>
                                    <td><?php echo $comp['id']; ?></td>
                                    <td><?php echo htmlspecialchars($comp['complaint_type']); ?></td>
                                    <td><?php echo $comp['complaint_date']; ?></td>
                                    <td><?php echo $comp['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No complaints found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Announcements</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($announcements) > 0): ?>
                            <?php foreach($announcements as $ann): ?>
                                <tr>
                                    <td><?php echo $ann['id']; ?></td>
                                    <td><?php echo htmlspecialchars($ann['title']); ?></td>
                                    <td><?php echo htmlspecialchars($ann['message']); ?></td>
                                    <td><?php echo $ann['date']; ?></td>
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
