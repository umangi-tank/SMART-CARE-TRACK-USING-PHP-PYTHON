<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}
?>
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

<style>
.sidebar {
    width: 220px;
    background: #fff;
    border-right: 1px solid #ddd;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
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
</style>
