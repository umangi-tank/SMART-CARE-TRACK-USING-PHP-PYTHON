<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

if (isset($_POST['save_faculty'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $school = $_POST['school'];
    $department = $_POST['department'];
    $program = $_POST['program'];
    $semester_year = $_POST['semester_year'];
    $division = $_POST['division'];
    $class_counsellor = $_POST['class_counsellor'];
// Handle profile photo upload
$profile_photo = null;

if (!empty($_FILES['profile_photo']['name'])) {

    $target_dir = "uploads/";  // only uploads folder

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Original filename
    $filename = $_FILES['profile_photo']['name'];

    // Final storage path with uploads/
    $target_file = $target_dir . $filename;

    // Move uploaded file
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        $profile_photo = $target_file;  // ✅ uploads/filename.jpg
    }
}


    $sql = "INSERT INTO faculty (full_name, email, mobile, password, school, department, program, semester_year, division, class_counsellor, profile_photo)
            VALUES ('$full_name', '$email', '$mobile', '$password', '$school', '$department', '$program', '$semester_year', '$division', '$class_counsellor', '$profile_photo')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Faculty Registered Successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Faculty</title>
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
h2 {
    color:#b71c1c;
    margin-bottom:20px;
}
.form-section {
    background:#fff;
    border-radius:10px;
    padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    margin-bottom:30px;
}
.form-section h4 {
    color:#b71c1c;
    border-bottom:2px solid #b71c1c;
    padding-bottom:5px;
    margin-bottom:20px;
}
.btn-submit {
    background:#b71c1c;
    color:#fff;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    font-weight:bold;
}
.btn-submit:hover {
    background:#880e4f;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h2>Add Faculty Details</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <h4>Profile Photo</h4>
            <div class="mb-3">
                <input type="file" name="profile_photo" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="form-section">
            <h4>Faculty Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4>Academic Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">School</label>
                    <input type="text" name="school" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <input type="text" name="program" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Semester / Year</label>
                    <input type="text" name="semester_year" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Faculty of Which Division</label>
                    <input type="text" name="division" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Which Class of Class Counsellor</label>
                    <input type="text" name="class_counsellor" class="form-control" placeholder="e.g., 7CEA or 5CSEB" required>
                </div>
            </div>
        </div>

        <button type="submit" name="save_faculty" class="btn-submit">Save Faculty</button>
    </form>
</div>

</body>
</html>
