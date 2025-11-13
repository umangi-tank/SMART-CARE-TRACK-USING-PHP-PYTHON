<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

include 'db_connect.php'; // DB connection

$user_email = $_SESSION['faculty_email'];

// Fetch faculty announcements (audience = 'faculty' or 'all')
$sql = "SELECT * FROM announcements WHERE audience IN ('faculty','all') ORDER BY created_at DESC";
$result = $mysqli->query($sql);

$announcements = [];
if($result){
    while($row = $result->fetch_assoc()){
        $announcements[] = $row;
    }
}
$result->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Announcements - Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0; padding:0;
}
.sidebar { 
    width:240px; 
    position:fixed; 
    top:0; left:0; 
    min-height:100vh; 
    background:#fff; 
    border-right:1px solid #ddd; 
    padding-top:20px; 
}
.sidebar a { display:block; padding:12px 20px; color:#333; text-decoration:none; border-radius:6px; margin:5px 10px; }
.sidebar a i { margin-right:10px; font-size:16px; }
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }

.content { margin-left:240px; padding:20px; }
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}

/* Table Styling */
.table-bordered {
    border: 2px solid #b71c1c;
}
.table-bordered th, .table-bordered td {
    vertical-align: middle;
}
.table-bordered tbody tr:hover {
    background-color: #f2f2f2;
}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <h3 style="color:#b71c1c; font-weight:bold;">Announcements</h3>
    </div>

    <!-- Faculty Announcements Table -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Sr No.</th>
                <th>Title</th>
                <th>Message</th>
                <th>Date</th>
                <th>Time</th>
                <th>From</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($announcements)): ?>
                <tr><td colspan="6" class="text-center">No announcements available.</td></tr>
            <?php else: foreach($announcements as $index => $ann): ?>
            <tr>
                <td><?= $index + 1; ?></td>
                <td><?= htmlspecialchars($ann['title'] ?? ''); ?></td>
                <td><?= nl2br(htmlspecialchars($ann['message'] ?? '')); ?></td>
                <td><?= date('d-m-Y', strtotime($ann['date'] ?? '')); ?></td>
                <td><?= date('h:i A', strtotime($ann['time'] ?? '')); ?></td>
                <td><?= htmlspecialchars($ann['from_email'] ?? ''); ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
