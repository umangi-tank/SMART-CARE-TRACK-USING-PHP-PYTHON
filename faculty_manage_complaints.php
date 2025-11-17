<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

include 'db_connect.php'; // include your DB connection

$faculty_email = $_SESSION['faculty_email'];

// 1️⃣ Get faculty's class_counsellor value from DB
$facultyQuery = $mysqli->prepare("SELECT class_counsellor FROM faculty WHERE email = ?");
$facultyQuery->bind_param("s", $faculty_email);
$facultyQuery->execute();
$facultyResult = $facultyQuery->get_result();

if ($facultyResult->num_rows > 0) {
    $facultyRow = $facultyResult->fetch_assoc();
    $faculty_class = $facultyRow['class_counsellor'];
} else {
    $faculty_class = ""; // fallback if no record found
}

// 2️⃣ Fetch complaints for students in this class
$complaintsQuery = $mysqli->prepare("SELECT * FROM complaints WHERE semester_year = ? ORDER BY id DESC");
$complaintsQuery->bind_param("s", $faculty_class);
$complaintsQuery->execute();
$complaints = $complaintsQuery->get_result();

// 3️⃣ Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complaint_id'])) {
    $complaintId = $_POST['complaint_id'];
    $status = $_POST['status'];
    $reply = $_POST['reply'];

    $update = $mysqli->prepare("
        UPDATE complaints 
        SET status = ?, 
            description = CONCAT(description, '\n\n--- Faculty Reply: ', ?) 
        WHERE id = ?
    ");
    $update->bind_param("ssi", $status, $reply, $complaintId);
    $update->execute();

    // ✅ Fixed Alert Script
    echo "<script>alert('Reply sent successfully'); window.location='faculty_manage_complaints.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Complaints</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f9f9f9; font-family: "Gill Sans","Gill Sans MT",Calibri,sans-serif; }
.content { margin-left: 230px; padding: 30px; }
.table th, .table td { vertical-align: middle; }
.status-resolved { color: green; font-weight: bold; }
.status-pending { color: red; font-weight: bold; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.page-header h3 { color: #b71c1c; font-weight: bold; }
.card { border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.modal-header { background-color: #b71c1c; }
.modal-header h5 { color: white; }
.btn-outline-primary { border-color: #b71c1c; color: #b71c1c; }
.btn-outline-primary:hover { background-color: #b71c1c; color: white; }
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="page-header">
        <h3>Manage Complaints (<?= htmlspecialchars($faculty_class) ?>)</h3>
    </div>

    <div class="card p-4">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Complaint Type</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($complaints && $complaints->num_rows > 0): ?>
                    <?php while ($comp = $complaints->fetch_assoc()): ?>
                    <tr>
                        <td><?= $comp['id']; ?></td>
                        <td><?= htmlspecialchars($comp['student_name']); ?></td>
                        <td><?= htmlspecialchars($comp['student_email']); ?></td>
                        <td><?= htmlspecialchars($comp['complaint_type']); ?></td>
                        <td><?= htmlspecialchars($comp['complaint_date']); ?></td>
                        <td style="text-align:left;"><?= nl2br(htmlspecialchars($comp['description'])); ?></td>
                        <td>
                            <?php
                                $status = $comp['status'] ?? 'Rejected';
                                if ($status === 'Resolved') {
                                    echo "<span class='status-resolved'>Resolved</span>";
                                } else {
                                    echo "<span class='status-pending'>Rejected</span>";
                                }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#replyModal" 
                                data-id="<?= $comp['id']; ?>" 
                                data-name="<?= htmlspecialchars($comp['student_name']); ?>" 
                                data-email="<?= htmlspecialchars($comp['student_email']); ?>" 
                                data-type="<?= htmlspecialchars($comp['complaint_type']); ?>" 
                                data-date="<?= htmlspecialchars($comp['complaint_date']); ?>" 
                                data-description="<?= htmlspecialchars($comp['description']); ?>">
                                <i class="bi bi-reply-fill"></i> Reply
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No complaints found for your class (<?= htmlspecialchars($faculty_class) ?>).</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="replyModalLabel">Reply to Complaint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="complaint_id" id="complaint_id">
            <p><strong>Student:</strong> <span id="modal_name"></span></p>
            <p><strong>Email:</strong> <span id="modal_email"></span></p>
            <p><strong>Complaint Type:</strong> <span id="modal_type"></span></p>
            <p><strong>Date:</strong> <span id="modal_date"></span></p>
            <p><strong>Description:</strong> <span id="modal_description"></span></p>

            <div class="mb-3 mt-3">
                <label for="reply" class="form-label">Your Reply</label>
                <textarea name="reply" id="reply" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label><br>
                <input type="radio" name="status" value="Resolved" required> Resolved
                <input type="radio" name="status" value="Rejected" class="ms-3" required checked> Rejected
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Send Reply</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const replyModal = document.getElementById('replyModal');
replyModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  document.getElementById('complaint_id').value = button.getAttribute('data-id');
  document.getElementById('modal_name').textContent = button.getAttribute('data-name');
  document.getElementById('modal_email').textContent = button.getAttribute('data-email');
  document.getElementById('modal_type').textContent = button.getAttribute('data-type');
  document.getElementById('modal_date').textContent = button.getAttribute('data-date');
  document.getElementById('modal_description').textContent = button.getAttribute('data-description');
});
</script>

</body>
</html>
