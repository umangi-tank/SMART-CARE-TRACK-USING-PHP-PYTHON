<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}
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

/* Sidebar styling */
.sidebar {
    width: 220px;
    background: #fff;
    border-right: 1px solid #ddd;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
}
.sidebar .sidebar-brand { text-align:center; margin-bottom:30px; }
.brand-rku { color:#b71c1c; margin:0; font-size:24px; }
.brand-admin { color:#444; margin:0; font-size:16px; }
.sidebar a {
    display:block;
    padding:12px 20px;
    color:#333;
    text-decoration:none;
    margin:5px 10px;
    border-radius:6px;
    transition:0.3s;
}
.sidebar a i { margin-right:10px; }
.sidebar a:hover, .sidebar a.active {
    background:#b71c1c;
    color:#fff;
    font-weight:bold;
}
.logout-btn {
    margin:20px 10px;
    width: calc(100% - 20px);
    background:#b71c1c;
    color:#fff;
    border:none;
    padding:10px;
    border-radius:6px;
    cursor:pointer;
}
.logout-btn:hover { background:#880e4f; }

/* Main content */
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

/* Dashboard cards layout */
.dashboard-cards {
    display:flex;
    flex-wrap:wrap;
    gap:25px;
    justify-content:flex-start;
}
.dashboard-cards .card {
    flex: 1 1 220px;
    max-width: 250px;
    min-height: 150px;
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    border-top: 5px solid #b71c1c;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: 0.3s;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.dashboard-cards .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.dashboard-cards .card h3 {
    font-size: 18px;
    color: #b71c1c;
    margin-bottom: 10px;
}
.dashboard-cards .card p {
    font-size: 14px;
    color: #555;
}
@media (max-width: 900px) {
    .dashboard-cards { justify-content: center; }
    .dashboard-cards .card { max-width: 45%; }
}
@media (max-width: 600px) {
    .dashboard-cards .card { max-width: 90%; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h2 class="brand-rku">RKU</h2>
        <h3 class="brand-admin">Admin</h3>
    </div>

    <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a>
    <a href="add_faculty.php"><i class="fas fa-user-plus"></i> Add Faculty</a>
    <a href="manage.php"><i class="fas fa-users-cog"></i> Manage</a>
    <a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a>
    <a href="attendance.php"><i class="fas fa-user-check"></i> Attendance</a>
    
    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>

    <form method="post" action="logout.php">
        <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
</div>

<!-- Main content -->
<div class="content">
    <div class="dashboard-header">
        <h2>Admin Dashboard</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <div class="dashboard-cards">
        <div class="card" onclick="location.href='admin_dashboard.php'">
            <h3><i class="fas fa-tachometer-alt"></i> Dashboard</h3>
            <p>Overview of all admin activities.</p>
        </div>
        <div class="card" onclick="location.href='add_student.php'">
            <h3><i class="fas fa-user-plus"></i> Add Student</h3>
            <p>Add new student accounts and details.</p>
        </div>
        <div class="card" onclick="location.href='add_faculty.php'">
            <h3><i class="fas fa-user-plus"></i> Add Faculty</h3>
            <p>Add new faculty accounts and details.</p>
        </div>
        <div class="card" onclick="location.href='manage.php'">
            <h3><i class="fas fa-users-cog"></i> Manage</h3>
            <p>Edit or remove students and faculty details.</p>
        </div>
        <div class="card" onclick="location.href='timetable.php'">
            <h3><i class="fas fa-calendar-alt"></i> Timetable</h3>
            <p>View and edit class schedules.</p>
        </div>
        <div class="card" onclick="location.href='attendance.php'">
            <h3><i class="fas fa-user-check"></i> Attendance</h3>
            <p>Mark and monitor student attendance.</p>
        </div>
        <div class="card" onclick="location.href='review.php'">
            <h3><i class="fas fa-star"></i> Review</h3>
            <p>Check feedback and reviews submitted.</p>
        </div>
        <div class="card" onclick="location.href='settings.php'">
            <h3><i class="fas fa-cog"></i> Settings</h3>
            <p>Update admin account settings.</p>
        </div>
    </div>
</div>

</body>
</html>
