<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample announcements
$announcements = [
    [
        'title' => 'Rasotsav 2025 Celebration!',
        'date' => '2025-10-11',
        'time' => '5:30 PM',
        'from' => 'SOAC (RK University) <soac@rku.ac.in>',
        'message' => 'The wait is finally over! Our most traditional and most awaited celebration – Rasotsav 2025 – is around the corner. Team SOAC welcomes you to celebrate the grand tradition of RKU with energy, unity, and festive vibes. Registrations are already open. Venue: RK University Main Campus. Register at: http://www.rku.ac.in/rasotsav'
    ],
    [
        'title' => 'Library Workshop Announcement',
        'date' => '2025-10-05',
        'time' => '2:00 PM',
        'from' => 'Library Team <library@rku.ac.in>',
        'message' => 'Join our special workshop on digital library resources. Enhance your research skills and learn to access journals efficiently. Venue: Central Library Hall.'
    ],
    [
        'title' => 'Sports Fest Reminder',
        'date' => '2025-10-08',
        'time' => '10:00 AM',
        'from' => 'Sports Committee <sports@rku.ac.in>',
        'message' => 'Get ready for the inter-college sports fest! Make sure to register your team. Venue: University Sports Ground.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements - Student Dashboard</title>
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
.sidebar .arc { font-size:20px; font-weight:bold; margin-bottom:15px; letter-spacing:1px; }
.sidebar .brand-rku { color:#b71c1c; }
.sidebar .brand-soac { color:#444; }
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
    cursor: pointer;
}
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Recent announcements</p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Announcements Table -->
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
            <?php foreach($announcements as $index => $ann): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($ann['title']); ?></td>
                <td><?php echo htmlspecialchars($ann['message']); ?></td>
                <td><?php echo htmlspecialchars($ann['date']); ?></td>
                <td><?php echo htmlspecialchars($ann['time']); ?></td>
                <td><?php echo htmlspecialchars($ann['from']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
