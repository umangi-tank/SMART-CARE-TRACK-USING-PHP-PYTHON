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
<script>
function showSuccessAlert() {
    alert("Faculty Registered Successfully!");
}
</script>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h2>Add Faculty Details</h2>

    <form onsubmit="showSuccessAlert(); return false;">
        <div class="form-section">
            <h4>Faculty Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4>Academic Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">School</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Semester / Year</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Faculty of Which Division</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Which Class of Class Counsellor</label>
                    <input type="text" class="form-control" placeholder="e.g., 7CEA or 5CSEB" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">Save Faculty</button>
    </form>
</div>

</body>
</html>
