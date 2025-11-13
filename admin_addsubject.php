<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Handle Add Subject form submit
$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_subject') {
    $subject_name = trim($_POST['subject_name'] ?? '');
    $semester = trim($_POST['semester'] ?? '');

    if ($subject_name === '') $errors[] = "Subject name is required.";
    if ($semester === '' || !in_array($semester, ['1','2','3','4','5','6','7','8'])) $errors[] = "Please select a valid semester.";

    if (empty($errors)) {
        // Check duplicate
        $stmt = $mysqli->prepare("SELECT id FROM subject WHERE subject_name = ? AND semester = ?");
        $stmt->bind_param('ss', $subject_name, $semester);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "This subject already exists for the selected semester.";
        } else {
            $ins = $mysqli->prepare("INSERT INTO subject (subject_name, semester) VALUES (?, ?)");
            $ins->bind_param('ss', $subject_name, $semester);
            if ($ins->execute()) {
                $success = "✅ Subject added successfully!";
            } else {
                $errors[] = "Database error: " . $ins->error;
            }
            $ins->close();
        }
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    if ($del_id > 0) {
        $del = $mysqli->prepare("DELETE FROM subject WHERE id = ?");
        $del->bind_param('i', $del_id);
        $del->execute();
        $del->close();
        header("Location: admin_addsubject.php");
        exit;
    }
}

// Fetch subjects
$subjects_res = $mysqli->query("SELECT * FROM subject ORDER BY semester ASC, subject_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Add Subject</title>
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
.page-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}
.page-header h2 { color:#b71c1c; font-weight:bold; }
.page-header .welcome { color:#444; font-weight:bold; }

.card {
    background:#fff;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    padding:25px;
    margin-bottom:30px;
}
.card h4 {
    color:#b71c1c;
    margin-bottom:20px;
}
.table th {
    background:#b71c1c;
    color:#fff;
}
.btn-primary {
    background-color:#b71c1c;
    border:none;
}
.btn-primary:hover {
    background-color:#8e1111;
}
.btn-danger {
    background-color:#dc3545;
    border:none;
}
.btn-danger:hover {
    background-color:#b71c1c;
}
.alert-success {
    background-color:#d4edda;
    color:#155724;
    border-left:5px solid #28a745;
}
.alert-danger {
    background-color:#f8d7da;
    color:#721c24;
    border-left:5px solid #dc3545;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2><i class="fas fa-book"></i> Manage Subjects</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <!-- Add Subject Form -->
    <div class="card">
        <h4><i class="fas fa-plus-circle"></i> Add New Subject</h4>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">
            <input type="hidden" name="action" value="add_subject">
            <div class="col-md-6">
                <label class="form-label">Subject Name</label>
                <input type="text" name="subject_name" class="form-control" required maxlength="150"
                    value="<?= isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="">Select Semester</option>
                    <?php for ($i=1;$i<=8;$i++): 
                        $sel = (isset($_POST['semester']) && $_POST['semester']==$i) ? 'selected' : '';
                    ?>
                        <option value="<?= $i ?>" <?= $sel ?>>Sem <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit"><i class="fas fa-plus"></i> Add Subject</button>
            </div>
        </form>
    </div>

    <!-- Subject List -->
    <div class="card">
        <h4><i class="fas fa-list"></i> Subjects List</h4>
        <?php if ($subjects_res && $subjects_res->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject Name</th>
                            <th>Semester</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; while($row=$subjects_res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['subject_name']) ?></td>
                            <td>Sem <?= htmlspecialchars($row['semester']) ?></td>
                            <td>
                                <a href="admin_addsubject.php?delete_id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure to delete this subject?')">
                                   <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No subjects found. Add some above.</div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
