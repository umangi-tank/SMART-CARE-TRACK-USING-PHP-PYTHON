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
<title>Add Student</title>
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
    margin-left:220px; /* sidebar width */
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
    <h2>Add Student Details</h2>

    <form method="post" action="save_student.php">

        <!-- Personal Info -->
        <div class="form-section">
            <h4>Personal Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Enter Full Name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">As Per Marksheet Name</label>
                    <input type="text" name="marksheet_name" class="form-control" placeholder="Enter Name as per Marksheet">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="father_name" class="form-control" placeholder="Enter Father Name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mother Name</label>
                    <input type="text" name="mother_name" class="form-control" placeholder="Enter Mother Name">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option selected disabled>Choose Gender</option>
                        <option>Female</option>
                        <option>Male</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Aadhar Card Number</label>
                    <input type="text" name="aadhar" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Blood Group</label>
                    <input type="text" name="blood_group" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Name As Per Aadhar Card</label>
                    <input type="text" name="aadhar_name" class="form-control">
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="form-section">
            <h4>Contact Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter Email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" placeholder="e.g. OBC/General/SC/ST">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Father Mobile No.</label>
                    <input type="text" name="father_mobile" class="form-control" placeholder="Enter Father's Mobile">
                </div>
                <div class="col-12">
                    <label class="form-label">Address Line 1</label>
                    <input type="text" name="address1" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Address Line 2</label>
                    <input type="text" name="address2" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="India">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pin Code</label>
                    <input type="text" name="pincode" class="form-control">
                </div>
            </div>
        </div>

        <!-- Academic Details -->
        <div class="form-section">
            <h4>Academic Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">School</label>
                    <input type="text" name="school" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <input type="text" name="program" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Semester/Year</label>
                    <input type="text" name="semester" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Division</label>
                    <input type="text" name="division" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Roll No</label>
                    <input type="text" name="roll_no" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission No</label>
                    <input type="text" name="admission_no" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Enrollment No.</label>
                    <input type="text" name="enrollment_no" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Admission Year</label>
                    <input type="text" name="admission_year" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Admission Type</label>
                    <input type="text" name="admission_type" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Internet Username</label>
                    <input type="text" name="internet_username" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Internet Password</label>
                    <input type="text" name="internet_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Institute Email-ID</label>
                    <input type="text" name="institute_email" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Institute Password</label>
                    <input type="text" name="institute_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">APAAR ID / ABC ID</label>
                    <input type="text" name="apaar_id" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Anti-Ragging Registration Number</label>
                    <input type="text" name="anti_ragging" class="form-control">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">Save Student</button>

    </form>
</div>

</body>
</html>
