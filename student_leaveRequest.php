<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample leave types
$leaveTypes = ['Sick Leave', 'Casual Leave', 'Emergency Leave'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Leave Request</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0;
    padding:0;
}

/* Sidebar */
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
.sidebar .arc {
    font-size:20px;
    font-weight:bold;
    margin-bottom:15px;
    letter-spacing:1px;
}
.sidebar .brand-rku { color:#b71c1c; }
.sidebar .brand-soac { color:#444; }
.sidebar a { display:block; padding:12px 20px; color:#333; text-decoration:none; border-radius:6px; margin:5px 10px; }
.sidebar a i { margin-right:10px; font-size:16px; }
.sidebar a:hover, .sidebar a.active { background:#b71c1c; color:#fff; font-weight:bold; }

/* Content */
.content { margin-left:240px; padding:20px; }

/* Header */
.dashboard-header { margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; }
.logout-btn {background:#b71c1c; color:#fff; border:none; padding:6px 12px; border-radius:4px; cursor:pointer;}
.logout-btn:hover {background:#880e4f;}

/* Form styling */
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
.form-label { font-weight:bold; }
.btn-submit { background:#b71c1c; color:#fff; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;}
.btn-submit:hover { background:#880e4f; }

/* Responsive */
@media (max-width:768px) {
    .sidebar { width:200px; }
    .content { margin-left:200px; padding:15px; }
    .card { padding:20px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<?php include "student_sidebar.php"; ?>

<!-- Content -->
<div class="content">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p class="text-muted">Submit your leave request below</p>
        </div>
        <form method="post" action="index.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Leave Request Form -->
    <div class="card">
        <h3>Student Leave Request Form</h3>
        <form id="leaveForm">
            <div class="mb-3">
                <label for="leaveType" class="form-label">Leave Type</label>
                <select name="leaveType" id="leaveType" class="form-select" required>
                    <option value="">Select Leave Type</option>
                    <?php foreach($leaveTypes as $type): ?>
                        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="fromDate" class="form-label">From</label>
                <input type="date" name="fromDate" id="fromDate" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="toDate" class="form-label">To</label>
                <input type="date" name="toDate" id="toDate" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <textarea name="reason" id="reason" class="form-control" rows="4" placeholder="Enter reason for leave" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById("leaveForm").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent form submission
    alert("✅ Leave request submitted successfully!");
    this.reset(); // optional: form fields reset after alert
});
</script>

</body>
</html>
