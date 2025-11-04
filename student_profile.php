<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample profile data
$profile = [
    'personal' => [
        'Full Name' => 'TANK UMANGI ASHOKBHAI',
        'As Per Marksheet Name' => 'TANK UMANGI ASHOKBHAI',
        'Father Name' => 'ASHOKBHAI',
        'Mother Name' => 'NEHABEN',
        'Gender' => 'Female',
        'Date of Birth' => '12/04/2005',
        'Aadhar Card Number' => '908482979845',
        'Blood Group' => 'NA',
        'Name As Per Aadhar Card' => 'TANK UMANGI ASHOKBHAI',
        'Email' => 'utank285@rku.ac.in',
        'Mobile Number' => '9173914174',
        'Category' => 'OBC/SEBC'
    ],
    'contact' => [
        'Address Line 1' => 'SHREE LAXMI NARAYAN , SANTOSH PARK , STREET NO 4 , KOTHARIYA ROAD , RAJKOT',
        'Address Line 2' => 'SHREE LAXMI NARAYAN , SANTOSH PARK , STREET NO 4 , KOTHARIYA ROAD , RAJKOT',
        'Father Mobile No.' => '7623045838',
        'City' => 'RAJKOT',
        'State' => '1',
        'Country' => 'INDIA',
        'Pin Code' => '360002'
    ],
    'academic' => [
        'School' => 'SCHOOL OF ENGINEERING',
        'Department' => 'Computer Engineering',
        'Program' => 'BACHELOR OF TECHNOLOGY IN COMPUTER ENGINEERING',
        'Semester/Year' => 'Sem-VII',
        'Division' => 'A (7CEA)',
        'Roll No' => '64',
        'Admission No' => '232411020041',
        'Enrollment No.' => '23SOECE13023',
        'Admission Year' => '2023',
        'Admission type' => 'D to D',
        'Internet Username' => '232411020041',
        'Internet Password' => '656998',
        'Institute Email-ID' => '-',
        'Institute Password' => '-',
        'APAAR ID / ABC ID' => '960734694676',
        'Anti-Ragging Registration Number' => '-'
    ]
];
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
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Your profile details</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <img id="profilePic" src="default-profile.png" alt="Profile Picture">
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
            <?php foreach($profile['personal'] as $key => $value): ?>
                <tr>
                    <th><?php echo $key; ?></th>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Contact Details -->
    <div class="profile-section">
        <h4>Contact Details</h4>
        <table class="table table-bordered">
            <tbody>
            <?php foreach($profile['contact'] as $key => $value): ?>
                <tr>
                    <th><?php echo $key; ?></th>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Academic Details -->
    <div class="profile-section">
        <h4>Academic Details</h4>
        <table class="table table-bordered">
            <tbody>
            <?php foreach($profile['academic'] as $key => $value): ?>
                <tr>
                    <th><?php echo $key; ?></th>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
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
