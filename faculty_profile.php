<?php
session_start();
include 'db_connect.php'; // DB connection file

// Check if faculty is logged in
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

$faculty_email = $_SESSION['faculty_email'];

// Fetch faculty data from DB
$sql = "SELECT * FROM faculty WHERE email = ?";
$stmt = $mysqli->prepare($sql); // <- $conn replaced by $mysqli
$stmt->bind_param("s", $faculty_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Faculty not found!'); window.location='faculty_login.php';</script>";
    exit();
}

$faculty = $result->fetch_assoc();

// Determine profile photo path
$profile_photo = 'default-profile.png'; // default
if (!empty($faculty['profile_photo'])) {
    if (file_exists($faculty['profile_photo'])) {
        $profile_photo = $faculty['profile_photo'];
    } else {
        $possible_path = 'uploads/' . basename($faculty['profile_photo']);
        if (file_exists($possible_path)) {
            $profile_photo = $possible_path;
        }
    }
}
?>


?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Faculty Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; padding:0; }
.sidebar { width:240px; position:fixed; top:0; left:0; min-height:100vh; background:#fff; border-right:1px solid #ddd; padding-top:20px; }
.sidebar a { display:block; padding:12px 20px; color:#333; text-decoration:none; border-radius:6px; margin:5px 10px; }
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }
.content { margin-left:240px; padding:20px; }
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}
.profile-header {display:flex; align-items:center; gap:20px; margin-bottom:30px;}
.profile-header img {width:150px; height:150px; object-fit:cover; border-radius:50%; border:3px solid #b71c1c;}
.profile-section {margin-bottom:30px;}
.profile-section h4 {color:#b71c1c; margin-bottom:15px;}
.table-bordered {border:2px solid #b71c1c;}
.table-bordered th, .table-bordered td {vertical-align: middle;}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h2>Faculty Profile</h2>
            <p class="text-muted">View your details</p>
        </div>
        <form method="post" action="faculty_login.php">
            <button class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Photo">
    </div>

    <!-- Personal Information -->
    <div class="profile-section">
        <h4>Faculty Information</h4>
        <table class="table table-bordered">
            <tbody>
                <tr><th>Full Name</th><td><?php echo htmlspecialchars($faculty['full_name']); ?></td></tr>
                <tr><th>Email</th><td><?php echo htmlspecialchars($faculty['email']); ?></td></tr>
                <tr><th>Mobile Number</th><td><?php echo htmlspecialchars($faculty['mobile']); ?></td></tr>
                <tr><th>Password</th><td>********</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Academic Details -->
    <div class="profile-section">
        <h4>Academic Details</h4>
        <table class="table table-bordered">
            <tbody>
                <tr><th>School</th><td><?php echo htmlspecialchars($faculty['school']); ?></td></tr>
                <tr><th>Department</th><td><?php echo htmlspecialchars($faculty['department']); ?></td></tr>
                <tr><th>Program</th><td><?php echo htmlspecialchars($faculty['program']); ?></td></tr>
                <tr><th>Semester / Year</th><td><?php echo htmlspecialchars($faculty['semester_year']); ?></td></tr>
                <tr><th>Faculty of Which Division</th><td><?php echo htmlspecialchars($faculty['division']); ?></td></tr>
                <tr><th>Class Counsellor Of</th><td><?php echo htmlspecialchars($faculty['class_counsellor']); ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
