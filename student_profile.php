<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Include DB connection
include 'db_connect.php';

$user_email = $_SESSION['user_email'];

// Fetch student details from DB
$stmt = $mysqli->prepare("SELECT * FROM students WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Student not found!'); window.location.href='login.php';</script>";
    exit();
}

$student = $result->fetch_assoc();

// Handle profile photo upload
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['name'] != '') {
    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // ORIGINAL filename only
    $file_name = basename($_FILES['profile_pic']['name']);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
        // Update in database
        $update_stmt = $conn->prepare("UPDATE students SET profile_photo = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $target_file, $user_email);
        $update_stmt->execute();
        $student['profile_photo'] = $target_file;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile</title>
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
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }

.content { margin-left:240px; padding:20px; }

.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}

.profile-header {
    display:flex;
    align-items:flex-start;
    margin-bottom:30px;
    flex-direction: column;
}
.profile-header img {
    width:150px;
    height:150px;
    object-fit:cover;
    border-radius:50%;
    border:3px solid #b71c1c;
}
.profile-header form {
    margin-top:10px;
}
.profile-header .btn-danger {
    width:100%;
}

.profile-section {
    margin-bottom:30px;
}
.profile-section h4 {
    color:#b71c1c;
    margin-bottom:15px;
}

.table-bordered {
    border:2px solid #b71c1c;
}
.table-bordered th, .table-bordered td {
    vertical-align: middle;
}
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($student['full_name']); ?></h2>
            <p class="text-muted">Your profile details</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <img id="profilePic" src="<?php echo !empty($student['profile_photo']) ? $student['profile_photo'] : 'default-profile.png'; ?>" alt="Profile Picture">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" class="form-control mb-2" onchange="previewImage(event)">
            <button type="submit" class="btn btn-danger">Update Profile</button>
        </form>
    </div>

    <!-- Personal Details -->
    <div class="profile-section">
        <h4>Personal Details</h4>
        <table class="table table-bordered">
            <tbody>
                <tr><th>Full Name</th><td><?php echo $student['full_name']; ?></td></tr>
                <tr><th>As Per Marksheet Name</th><td><?php echo $student['marksheet_name']; ?></td></tr>
                <tr><th>Father Name</th><td><?php echo $student['father_name']; ?></td></tr>
                <tr><th>Mother Name</th><td><?php echo $student['mother_name']; ?></td></tr>
                <tr><th>Gender</th><td><?php echo $student['gender']; ?></td></tr>
                <tr><th>Date of Birth</th><td><?php echo $student['dob']; ?></td></tr>
                <tr><th>Aadhar Card Number</th><td><?php echo $student['aadhar']; ?></td></tr>
                <tr><th>Blood Group</th><td><?php echo $student['blood_group']; ?></td></tr>
                <tr><th>Name As Per Aadhar Card</th><td><?php echo $student['aadhar_name']; ?></td></tr>
                <tr><th>Email</th><td><?php echo $student['email']; ?></td></tr>
                <tr><th>Mobile Number</th><td><?php echo $student['mobile']; ?></td></tr>
                <tr><th>Category</th><td><?php echo $student['category']; ?></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Contact Details -->
    <div class="profile-section">
        <h4>Contact Details</h4>
        <table class="table table-bordered">
            <tbody>
                <tr><th>Father Mobile No.</th><td><?php echo $student['father_mobile']; ?></td></tr>
                <tr><th>Address Line 1</th><td><?php echo $student['address1']; ?></td></tr>
                <tr><th>Address Line 2</th><td><?php echo $student['address2']; ?></td></tr>
                <tr><th>City</th><td><?php echo $student['city']; ?></td></tr>
                <tr><th>State</th><td><?php echo $student['state']; ?></td></tr>
                <tr><th>Country</th><td><?php echo $student['country']; ?></td></tr>
                <tr><th>Pin Code</th><td><?php echo $student['pincode']; ?></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Academic Details -->
    <div class="profile-section">
        <h4>Academic Details</h4>
        <table class="table table-bordered">
            <tbody>
                <tr><th>School</th><td><?php echo $student['school']; ?></td></tr>
                <tr><th>Department</th><td><?php echo $student['department']; ?></td></tr>
                <tr><th>Program</th><td><?php echo $student['program']; ?></td></tr>
                <tr><th>Semester/Year</th><td><?php echo $student['semester']; ?></td></tr>
                <tr><th>Division</th><td><?php echo $student['division']; ?></td></tr>
                <tr><th>Roll No</th><td><?php echo $student['roll_no']; ?></td></tr>
                <tr><th>Admission No</th><td><?php echo $student['admission_no']; ?></td></tr>
                <tr><th>Enrollment No.</th><td><?php echo $student['enrollment_no']; ?></td></tr>
                <tr><th>Admission Year</th><td><?php echo $student['admission_year']; ?></td></tr>
                <tr><th>Admission type</th><td><?php echo $student['admission_type']; ?></td></tr>
                <tr><th>Internet Username</th><td><?php echo $student['internet_username']; ?></td></tr>
                <tr><th>Internet Password</th><td><?php echo $student['internet_password']; ?></td></tr>
                <tr><th>Institute Email-ID</th><td><?php echo $student['institute_email']; ?></td></tr>
                <tr><th>Institute Password</th><td><?php echo $student['institute_password']; ?></td></tr>
                <tr><th>APAAR ID / ABC ID</th><td><?php echo $student['apaar_id']; ?></td></tr>
                <tr><th>Anti-Ragging Registration Number</th><td><?php echo $student['anti_ragging']; ?></td></tr>
            </tbody>
        </table>
    </div>

</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('profilePic');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
</body>
</html>
