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

/* Sidebar layout fix */
.content {
    margin-left:220px; /* adjust based on sidebar width */
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

/* Status summary boxes */
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

/* Mission & Vision Section */
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
                <p>1250</p>
            </div>
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>All Complaints</h4>
                <p>20</p>
            </div>
            <i class="fas fa-building"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>All Teachers</h4>
                <p>85</p>
            </div>
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="status-box">
            <div class="info">
                <h4>Pending Requests</h4>
                <p>5</p>
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
