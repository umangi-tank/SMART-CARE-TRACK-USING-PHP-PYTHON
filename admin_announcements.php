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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Announcements</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0;
    display:flex;
}

/* Sidebar layout fix */
.content {
    margin-left:220px;
    padding:30px;
    flex:1;
}

/* Page Header */
.page-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.page-header h2 { color:#b71c1c; }
.page-header .welcome { color:#444; font-weight:bold; }

/* Add Announcement Form */
.add-announcement {
    background:#fff;
    border-radius:10px;
    padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    margin-bottom:40px;
}
.add-announcement h3 {
    color:#b71c1c;
    margin-bottom:20px;
}
.add-announcement input, 
.add-announcement textarea {
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
}
.add-announcement button {
    background:#b71c1c;
    color:#fff;
    padding:12px 20px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
}
.add-announcement button:hover { background:#880e4f; }

/* Announcements Table */
.announcements-table {
    background:#fff;
    border-radius:10px;
    padding:20px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    overflow-x:auto;
}
.announcements-table table {
    width:100%;
    border-collapse:collapse;
}
.announcements-table th, 
.announcements-table td {
    padding:12px 15px;
    border-bottom:1px solid #ddd;
    text-align:left;
    font-size:14px;
}
.announcements-table th {
    background:#b71c1c;
    color:#fff;
    font-weight:bold;
}
.announcements-table tr:hover {
    background:#f1f1f1;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2>Announcements</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <!-- Add Announcement Form -->
    <div class="add-announcement">
        <h3><i class="fas fa-plus-circle"></i> Add New Announcement</h3>
        <form>
            <input type="text" placeholder="Title" required>
            <textarea rows="3" placeholder="Message" required></textarea>
            <input type="date" placeholder="Date" required>
            <input type="time" placeholder="Time" required>
            <input type="email" placeholder="From (Email)" required>
            <button type="submit"><i class="fas fa-paper-plane"></i> Add Announcement</button>
        </form>
    </div>

    <!-- Announcements Table -->
    <div class="announcements-table">
        <table>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>From (Email)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>New Semester Begins</td>
                    <td>Welcome to all students! The new semester will start from 10th November 2025.</td>
                    <td>01-11-2025</td>
                    <td>09:00 AM</td>
                    <td>admin@example.com</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Exam Schedule Released</td>
                    <td>The final semester exam schedule has been published. Check your department notice board.</td>
                    <td>28-10-2025</td>
                    <td>11:00 AM</td>
                    <td>admin@example.com</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Library Timings</td>
                    <td>The library will remain open from 9 AM to 6 PM during the semester.</td>
                    <td>25-10-2025</td>
                    <td>08:00 AM</td>
                    <td>admin@example.com</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
