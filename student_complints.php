<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Sample complaint types
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
            <p class="text-muted">Submit your complaint below</p>
        </div>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <!-- Complaint Form -->
    <div class="card">
        <h3>Student Complaint Form</h3>
        <form id="complaintForm">
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
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter complaint details" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn-submit">Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById("complaintForm").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent actual submission
    alert("✅ Complaint submitted successfully!");
    this.reset(); // optional: reset form fields after alert
});
</script>

</body>
</html>
