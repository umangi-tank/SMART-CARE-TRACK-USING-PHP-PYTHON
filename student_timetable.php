<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Weekly Time Table - Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }

.content { margin-left:240px; padding:20px; }
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}

/* Table styling */
.table-bordered {
    border: 2px solid #b71c1c;
    text-align: center;
}
.table-bordered th, .table-bordered td {
    vertical-align: middle;
}
.break-row {
    background-color: #f2f2f2;
    font-weight: bold;
}
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Weekly Time Table</h2>
            <p class="text-muted">Class schedule for Monday to Friday</p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Time Slot</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
            </tr>
        </thead>
        <tbody>
            <!-- 1st Slot -->
            <tr>
                <td>08:00 - 09:45</td>
                <td>CC Batch 1 (AKK)<br>CD Batch 2 + CD Batch 3 (MS)</td>
                <td>IoT (NC)<br>DL (CP)</td>
                <td>CS Batch 1 (MS)<br>CS Batch 2 (SS)</td>
                <td>CS Batch 1 (BD)<br>CS Batch 2 (MS)</td>
                <td>CC Batch 1 (VA)<br>CD Batch 2 (MS)<br>BD (CS)</td>
            </tr>

            <!-- Tea Break -->
            <tr class="break-row">
                <td>09:45 - 10:00 (Tea Break)</td>
                <td colspan="5">Tea Break</td>
            </tr>

            <!-- 2nd Slot -->
            <tr>
                <td>10:00 - 11:40</td>
                <td>CD Batch 1 (MS)<br>CC Batch 2 (AKK)<br>BD (CS)</td>
                <td>CS Batch 1 (SS)<br>CS Batch 2 (BD)</td>
                <td>IoT (NC) / Project (JP)<br>DL (CP) / Project (JP)</td>
                <td>IoT (NC)<br>DL (CP)</td>
                <td>CD Batch 1 + CD Batch 3 (MS)<br>CC Batch 2 (VA)</td>
            </tr>

            <!-- Lunch Break -->
            <tr class="break-row">
                <td>11:40 - 12:30 (Lunch Break)</td>
                <td colspan="5">Lunch Break</td>
            </tr>

            <!-- 3rd Slot (optional/blank) -->
            <tr>
                <td>12:30 - 14:10</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
