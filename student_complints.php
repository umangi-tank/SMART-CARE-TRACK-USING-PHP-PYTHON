<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; // include your DB connection file

$user_email = $_SESSION['user_email'];

// 🔹 Fetch student details from DB
$stmt = $mysqli->prepare("SELECT full_name, semester, email FROM students WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$student_name = $student ? $student['full_name'] : 'Unknown Student';
$student_semester = $student ? $student['semester'] : 'N/A';

// 🔹 Handle complaint form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_complaint'])) {
    $complaintType = $_POST['complaintType'];
    $complaintDate = $_POST['complaintDate'];
    $description = $_POST['description'];

    $insert = $mysqli->prepare("INSERT INTO complaints (student_name, student_email, semester_year, complaint_type, complaint_date, description) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("ssssss", $student_name, $user_email, $student_semester, $complaintType, $complaintDate, $description);

    if ($insert->execute()) {
        echo "<script>alert('✅ Complaint submitted successfully!');</script>";
    } else {
        echo "<script>alert('❌ Error submitting complaint. Please try again.');</script>";
    }
}

$complaintTypes = ['Hostel Issue', 'Library Issue', 'Mess Complaint', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Complaint Box</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0;
    padding:0;
}
.sidebar {
    width:240px;
    position:fixed;
    top:0;
    left:0;
    min-height:100vh;
    background:#fff;
    border-right:1px solid #ddd;
    padding-top:20px;
}
.sidebar a {
    display:block; padding:12px 20px; color:#333;
    text-decoration:none; border-radius:6px; margin:5px 10px;
}
.sidebar a:hover, .sidebar a.active {
    background:#b71c1c; color:#fff; font-weight:bold;
}
.content { margin-left:240px; padding:20px; }
.dashboard-header {
    margin-bottom:30px; display:flex;
    justify-content:space-between; align-items:center;
}
.logout-btn {
    background:#b71c1c; color:#fff;
    border:none; padding:6px 12px;
    border-radius:4px; cursor:pointer;
}
.logout-btn:hover {background:#880e4f;}
.card {
    border-radius:8px;
    padding:30px;
    background:#fff;
    border:1px solid #b71c1c;
    max-width:800px;
    margin:auto;
    box-shadow:0 4px 8px rgba(0,0,0,0.1);
}
.card h3 { margin-bottom:25px; text-align:center; }
.btn-submit {
    background:#b71c1c; color:#fff;
    border:none; padding:10px 20px;
    border-radius:5px; cursor:pointer;
}
.btn-submit:hover { background:#880e4f; }
</style>
</head>
<body>

<?php include "student_sidebar.php"; ?>

<div class="content">
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($student_name); ?></h2>
            <p class="text-muted">Submit your complaint below</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <div class="card">
        <h3>Student Complaint Form</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Student Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_name); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Semester / Year</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_semester); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="complaintType" class="form-label">Complaint Type</label>
                <select name="complaintType" id="complaintType" class="form-select" required>
                    <option value="">Select Complaint Type</option>
                    <?php foreach($complaintTypes as $type): ?>
                        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="complaintDate" class="form-label">Date</label>
                <input type="date" name="complaintDate" id="complaintDate" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter complaint details" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" name="submit_complaint" class="btn-submit">Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
