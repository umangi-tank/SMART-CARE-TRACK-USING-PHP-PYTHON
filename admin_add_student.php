<?php
session_start();
if (!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

if (isset($_POST['save_student'])) {

    // Collect form data
    $full_name       = $_POST['full_name'];
    $marksheet_name  = $_POST['marksheet_name'];
    $father_name     = $_POST['father_name'];
    $mother_name     = $_POST['mother_name'];
    $gender          = $_POST['gender'];
    $dob             = $_POST['dob'];
    $aadhar          = $_POST['aadhar'];
    $blood_group     = $_POST['blood_group'];
    $aadhar_name     = $_POST['aadhar_name'];
    $email           = $_POST['email'];
    $mobile          = $_POST['mobile'];
    $category        = $_POST['category'];
    $father_mobile   = $_POST['father_mobile'];
    $address1        = $_POST['address1'];
    $address2        = $_POST['address2'];
    $city            = $_POST['city'];
    $state           = $_POST['state'];
    $country         = $_POST['country'];
    $pincode         = $_POST['pincode'];
    $password        = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $school          = $_POST['school'];
    $department      = $_POST['department'];
    $program         = $_POST['program'];
    $semester        = $_POST['semester'];
    $division        = $_POST['division'];
    $roll_no         = $_POST['roll_no'];
    $admission_no    = $_POST['admission_no'];
    $enrollment_no   = $_POST['enrollment_no'];
    $admission_year  = $_POST['admission_year'];
    $admission_type  = $_POST['admission_type'];
    $internet_username = $_POST['internet_username'];
    $internet_password = $_POST['internet_password'];
    $institute_email   = $_POST['institute_email'];
    $institute_password= $_POST['institute_password'];
    $apaar_id          = $_POST['apaar_id'];
    $anti_ragging      = $_POST['anti_ragging'];

// Handle profile photo upload
$profile_photo = null;
if (!empty($_FILES['profile_photo']['name'])) {

    $target_dir = "uploads/";

    // Create folder if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // ORIGINAL filename only
    $file_name = basename($_FILES["profile_photo"]["name"]);
    $target_file = $target_dir . $file_name;

    // Upload
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $profile_photo = $target_file;
    }
}


    // Insert into database
    $sql = "INSERT INTO students 
    (profile_photo, full_name, marksheet_name, father_name, mother_name, gender, dob, aadhar, blood_group, aadhar_name, email, mobile, category, father_mobile, address1, address2, city, state, country, pincode, password, school, department, program, semester, division, roll_no, admission_no, enrollment_no, admission_year, admission_type, internet_username, internet_password, institute_email, institute_password, apaar_id, anti_ragging)
    VALUES 
    ('$profile_photo', '$full_name', '$marksheet_name', '$father_name', '$mother_name', '$gender', '$dob', '$aadhar', '$blood_group', '$aadhar_name', '$email', '$mobile', '$category', '$father_mobile', '$address1', '$address2', '$city', '$state', '$country', '$pincode', '$password', '$school', '$department', '$program', '$semester', '$division', '$roll_no', '$admission_no', '$enrollment_no', '$admission_year', '$admission_type', '$internet_username', '$internet_password', '$institute_email', '$institute_password', '$apaar_id', '$anti_ragging')";

    if ($mysqli->query($sql) === TRUE) {
        echo "<script>alert('✅ Student added successfully!');</script>";
    } else {
        echo "<script>alert('❌ Error: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Student</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; display:flex; }
.content { margin-left:220px; padding:30px; flex:1; }
h2 { color:#b71c1c; margin-bottom:20px; }
.form-section { background:#fff; border-radius:10px; padding:25px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-bottom:30px; }
.form-section h4 { color:#b71c1c; border-bottom:2px solid #b71c1c; padding-bottom:5px; margin-bottom:20px; }
.btn-submit { background:#b71c1c; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:bold; }
.btn-submit:hover { background:#880e4f; }
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h2>Add Student Details</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <h4>Profile Photo</h4>
            <input type="file" name="profile_photo" class="form-control" accept="image/*">
        </div>

        <div class="form-section">
            <h4>Personal Information</h4>
            <div class="row g-3">
                <div class="col-md-6"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                <div class="col-md-6"><label>As Per Marksheet Name</label><input type="text" name="marksheet_name" class="form-control"></div>
                <div class="col-md-6"><label>Father Name</label><input type="text" name="father_name" class="form-control"></div>
                <div class="col-md-6"><label>Mother Name</label><input type="text" name="mother_name" class="form-control"></div>
                <div class="col-md-4"><label>Gender</label><select name="gender" class="form-select"><option selected disabled>Choose Gender</option><option>Female</option><option>Male</option><option>Other</option></select></div>
                <div class="col-md-4"><label>Date of Birth</label><input type="date" name="dob" class="form-control"></div>
                <div class="col-md-4"><label>Aadhar Card Number</label><input type="text" name="aadhar" class="form-control"></div>
                <div class="col-md-4"><label>Blood Group</label><input type="text" name="blood_group" class="form-control"></div>
                <div class="col-md-8"><label>Name As Per Aadhar Card</label><input type="text" name="aadhar_name" class="form-control"></div>
            </div>
        </div>

        <div class="form-section">
            <h4>Contact Details</h4>
            <div class="row g-3">
                <div class="col-md-6"><label>Email</label><input type="email" name="email" class="form-control"></div>
                <div class="col-md-6"><label>Mobile Number</label><input type="text" name="mobile" class="form-control"></div>
                <div class="col-md-6"><label>Category</label><input type="text" name="category" class="form-control"></div>
                <div class="col-md-6"><label>Father Mobile No.</label><input type="text" name="father_mobile" class="form-control"></div>
                <div class="col-12"><label>Address Line 1</label><input type="text" name="address1" class="form-control"></div>
                <div class="col-12"><label>Address Line 2</label><input type="text" name="address2" class="form-control"></div>
                <div class="col-md-4"><label>City</label><input type="text" name="city" class="form-control"></div>
                <div class="col-md-4"><label>State</label><input type="text" name="state" class="form-control"></div>
                <div class="col-md-4"><label>Country</label><input type="text" name="country" class="form-control" value="India"></div>
                <div class="col-md-4"><label>Password</label><input type="text" name="password" class="form-control" required></div>
                <div class="col-md-4"><label>Pin Code</label><input type="text" name="pincode" class="form-control"></div>
            </div>
        </div>

        <div class="form-section">
            <h4>Academic Details</h4>
            <div class="row g-3">
                <div class="col-md-6"><label>School</label><input type="text" name="school" class="form-control"></div>
                <div class="col-md-6"><label>Department</label><input type="text" name="department" class="form-control"></div>
                <div class="col-md-6"><label>Program</label><input type="text" name="program" class="form-control"></div>
                <div class="col-md-6"><label>Semester/Year</label><input type="text" name="semester" class="form-control"></div>
                <div class="col-md-4"><label>Division</label><input type="text" name="division" class="form-control"></div>
                <div class="col-md-4"><label>Roll No</label><input type="text" name="roll_no" class="form-control"></div>
                <div class="col-md-4"><label>Admission No</label><input type="text" name="admission_no" class="form-control"></div>
                <div class="col-md-6"><label>Enrollment No.</label><input type="text" name="enrollment_no" class="form-control"></div>
                <div class="col-md-6"><label>Admission Year</label><input type="text" name="admission_year" class="form-control"></div>
                <div class="col-md-6"><label>Admission Type</label><input type="text" name="admission_type" class="form-control"></div>
                <div class="col-md-6"><label>Internet Username</label><input type="text" name="internet_username" class="form-control"></div>
                <div class="col-md-6"><label>Internet Password</label><input type="text" name="internet_password" class="form-control"></div>
                <div class="col-md-6"><label>Institute Email-ID</label><input type="text" name="institute_email" class="form-control"></div>
                <div class="col-md-6"><label>Institute Password</label><input type="text" name="institute_password" class="form-control"></div>
                <div class="col-md-6"><label>APAAR ID / ABC ID</label><input type="text" name="apaar_id" class="form-control"></div>
                <div class="col-md-6"><label>Anti-Ragging Registration Number</label><input type="text" name="anti_ragging" class="form-control"></div>
            </div>
        </div>

        <button type="submit" name="save_student" class="btn-submit">Save Student</button>
    </form>
</div>

</body>
</html>
