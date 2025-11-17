<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php'; // make sure $mysqli connection is here

// ---------------------- FETCH COUNTS FROM DATABASE ----------------------

// Count of all students
$result_students = $mysqli->query("SELECT COUNT(*) as total_students FROM students");
$students_count = $result_students->fetch_assoc()['total_students'] ?? 0;

// Count of all complaints
$result_complaints = $mysqli->query("SELECT COUNT(*) as total_complaints FROM complaints");
$complaints_count = $result_complaints->fetch_assoc()['total_complaints'] ?? 0;

// Count of all teachers
$result_teachers = $mysqli->query("SELECT COUNT(*) as total_teachers FROM faculty");
$teachers_count = $result_teachers->fetch_assoc()['total_teachers'] ?? 0;

// Count of pending requests (example: leave requests pending approval)
$result_pending = $mysqli->query("SELECT COUNT(*) as total_pending FROM leave_requests WHERE status='Pending'");
$pending_count = $result_pending->fetch_assoc()['total_pending'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0;
    display:flex;
}
.content {
    margin-left:220px;
    padding:30px;
    flex:1;
}
.dashboard-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.dashboard-header h2 { color:#b71c1c; }
.dashboard-header .welcome { color:#444; font-weight:bold; }

.status-boxes {
    display:flex;
    flex-wrap:wrap;
    gap:25px;
    margin-bottom:40px;
}
.status-box {
    flex:1 1 220px;
    min-width:200px;
    background:#fff;
    border-left:6px solid #b71c1c;
    border-radius:10px;
    padding:20px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:0.3s;
}
.status-box:hover {
    transform:translateY(-5px);
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}
.status-box i {
    font-size:30px;
    color:#b71c1c;
}
.status-box .info h4 {
    margin:0;
    font-size:18px;
    color:#444;
}
.status-box .info p {
    margin:0;
    font-size:22px;
    font-weight:bold;
    color:#b71c1c;
}

.mission-vision {
    background:#fff;
    border-radius:10px;
    padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    margin-bottom:40px;
}
.mission-vision h3 {
    color:#b71c1c;
    margin-bottom:15px;
}
.mission-vision p {
    color:#444;
    line-height:1.6;
}

@media (max-width: 900px) {
    .status-boxes { justify-content: center; }
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <!-- Status Boxes -->
    <div class="status-boxes">
        <div class="status-box">
            <div class="info">
                <h4>All Students</h4>
                <p><?php echo $students_count; ?></p>
            </div>
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>All Complaints</h4>
                <p><?php echo $complaints_count; ?></p>
            </div>
            <i class="fas fa-building"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>All Teachers</h4>
                <p><?php echo $teachers_count; ?></p>
            </div>
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>Pending Requests</h4>
                <p><?php echo $pending_count; ?></p>
            </div>
            <i class="fas fa-envelope-open-text"></i>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="mission-vision">
        <h3><i class="fas fa-bullseye"></i> Mission</h3>
        <p>Our mission is to provide an efficient, smart, and reliable system to manage student and faculty activities with transparency and technological excellence.</p>

        <h3><i class="fas fa-eye"></i> Vision</h3>
        <p>Our vision is to become a digital leader in academic management by integrating innovation, accuracy, and accessibility for all users.</p>
    </div>

</div>
</body>
</html>
