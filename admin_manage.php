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
<title>Manage Students & Faculty</title>
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
    margin-bottom:25px;
}
.table-section {
    background:#fff;
    border-radius:10px;
    padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    margin-bottom:40px;
}
.table-section h4 {
    color:#b71c1c;
    border-bottom:2px solid #b71c1c;
    padding-bottom:10px;
    margin-bottom:20px;
}
.table {
    border-radius:10px;
    overflow:hidden;
    font-size:14px;
}
.table th {
    background-color:#b71c1c;
    color:#fff;
    white-space:nowrap;
}
.table td {
    vertical-align:middle;
}
.btn-edit {
    background:#b71c1c;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    font-size:13px;
    transition:0.3s;
}
.btn-edit:hover {
    background:#880e4f;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h2>Manage Students & Faculty</h2>

    <!-- Student Table -->
    <div class="table-section">
        <h4><i class="fas fa-user-graduate"></i> Student Details</h4>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Program</th>
                        <th>Semester/Year</th>
                        <th>Division</th>
                        <th>Roll No</th>
                        <th>Admission No</th>
                        <th>Enrollment No</th>
                        <th>Admission Year</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Static Example Row -->
                    <tr>
                        <td>1</td>
                        <td>Riya Patel</td>
                        <td>Ramesh Patel</td>
                        <td>Anita Patel</td>
                        <td>Female</td>
                        <td>2003-05-14</td>
                        <td>riya@example.com</td>
                        <td>9876543210</td>
                        <td>RKU</td>
                        <td>Computer Science</td>
                        <td>B.Tech</td>
                        <td>5th</td>
                        <td>A</td>
                        <td>21CS45</td>
                        <td>12345</td>
                        <td>CS2021RKU45</td>
                        <td>2021</td>
                        <td>Rajkot</td>
                        <td>Gujarat</td>
                        <td>India</td>
                        <td><button class="btn-edit"><i class="fas fa-edit"></i> Edit</button></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Dev Sharma</td>
                        <td>Mahesh Sharma</td>
                        <td>Kiran Sharma</td>
                        <td>Male</td>
                        <td>2002-10-02</td>
                        <td>dev@example.com</td>
                        <td>9988776655</td>
                        <td>RKU</td>
                        <td>IT</td>
                        <td>B.Tech</td>
                        <td>3rd</td>
                        <td>B</td>
                        <td>21IT32</td>
                        <td>22321</td>
                        <td>IT2022RKU32</td>
                        <td>2022</td>
                        <td>Ahmedabad</td>
                        <td>Gujarat</td>
                        <td>India</td>
                        <td><button class="btn-edit"><i class="fas fa-edit"></i> Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Faculty Table -->
    <div class="table-section">
        <h4><i class="fas fa-chalkboard-teacher"></i> Faculty Details</h4>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Program</th>
                        <th>Semester/Year</th>
                        <th>Faculty of Which Division</th>
                        <th>Class Counsellor Of</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Static Example Rows -->
                    <tr>
                        <td>1</td>
                        <td>Dr. Neha Shah</td>
                        <td>neha.shah@example.com</td>
                        <td>9876543210</td>
                        <td>RKU</td>
                        <td>Computer Science</td>
                        <td>B.Tech</td>
                        <td>7th</td>
                        <td>A</td>
                        <td>7CEA</td>
                        <td><button class="btn-edit"><i class="fas fa-edit"></i> Edit</button></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Prof. Amit Joshi</td>
                        <td>amit.joshi@example.com</td>
                        <td>9988776655</td>
                        <td>RKU</td>
                        <td>Mechanical</td>
                        <td>B.E.</td>
                        <td>6th</td>
                        <td>B</td>
                        <td>6MEB</td>
                        <td><button class="btn-edit"><i class="fas fa-edit"></i> Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
