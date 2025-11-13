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

// 2️⃣ Fetch leave requests for students in this class
$leaveRequestsQuery = $mysqli->prepare("SELECT * FROM leave_requests WHERE semester_year = ? ORDER BY id DESC");
$leaveRequestsQuery->bind_param("s", $faculty_class);
$leaveRequestsQuery->execute();
$leaveRequests = $leaveRequestsQuery->get_result();

// 3️⃣ Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_id'])) {
    $leaveId = $_POST['leave_id'];
    $status = $_POST['status'];
    $reply = $_POST['reply'];

    $update = $mysqli->prepare("UPDATE leave_requests SET status = ?, reason = CONCAT(reason, '\n\n--- Faculty Reply: ', ?) WHERE id = ?");
    $update->bind_param("ssi", $status, $reply, $leaveId);
    $update->execute();

    echo "<script>alert('Reply sent successfully'); window.location='faculty_manage_leave_request.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Leave Requests</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f9f9f9; font-family: "Gill Sans","Gill Sans MT",Calibri,sans-serif; }
.content { margin-left: 230px; padding: 30px; }
.table th, .table td { vertical-align: middle; }
.status-approved {color: green; font-weight: bold;}
.status-rejected {color: red; font-weight: bold;}
.status-pending {color: orange; font-weight: bold;}
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
        <h3>Manage Leave Requests (<?= htmlspecialchars($faculty_class) ?>)</h3>
    </div>

    <div class="card p-4">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leaveRequests && $leaveRequests->num_rows > 0): ?>
                    <?php while ($req = $leaveRequests->fetch_assoc()): ?>
                    <tr>
                        <td><?= $req['id']; ?></td>
                        <td><?= htmlspecialchars($req['student_name']); ?></td>
                        <td><?= htmlspecialchars($req['student_email']); ?></td>
                        <td><?= htmlspecialchars($req['leave_type']); ?></td>
                        <td><?= htmlspecialchars($req['from_date']); ?></td>
                        <td><?= htmlspecialchars($req['to_date']); ?></td>
                        <td style="text-align:left;"><?= nl2br(htmlspecialchars($req['reason'])); ?></td>
                        <td>
                            <?php 
                                if ($req['status'] === 'Approved') echo "<span class='status-approved'>Approved</span>";
                                elseif ($req['status'] === 'Rejected') echo "<span class='status-rejected'>Rejected</span>";
                                else echo "<span class='status-pending'>Pending</span>";
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#replyModal" 
                                data-id="<?= $req['id']; ?>" 
                                data-student="<?= htmlspecialchars($req['student_name']); ?>" 
                                data-type="<?= htmlspecialchars($req['leave_type']); ?>" 
                                data-from="<?= htmlspecialchars($req['from_date']); ?>" 
                                data-to="<?= htmlspecialchars($req['to_date']); ?>" 
                                data-reason="<?= htmlspecialchars($req['reason']); ?>">
                                <i class="bi bi-reply-fill"></i> Reply
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">No leave requests found for your class (<?= htmlspecialchars($faculty_class) ?>).</td></tr>
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
          <h5 class="modal-title" id="replyModalLabel">Reply to Leave Request</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="leave_id" id="leave_id">
            <p><strong>Student:</strong> <span id="modal_student"></span></p>
            <p><strong>Type:</strong> <span id="modal_type"></span></p>
            <p><strong>From:</strong> <span id="modal_from"></span></p>
            <p><strong>To:</strong> <span id="modal_to"></span></p>
            <p><strong>Reason:</strong> <span id="modal_reason"></span></p>

            <div class="mb-3 mt-3">
                <label for="reply" class="form-label">Your Reply</label>
                <textarea name="reply" id="reply" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label><br>
                <input type="radio" name="status" value="Approved" required> Approve
                <input type="radio" name="status" value="Rejected" class="ms-3" required> Reject
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
const replyModal = document.getElementById('replyModal')
replyModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget
  document.getElementById('leave_id').value = button.getAttribute('data-id')
  document.getElementById('modal_student').textContent = button.getAttribute('data-student')
  document.getElementById('modal_type').textContent = button.getAttribute('data-type')
  document.getElementById('modal_from').textContent = button.getAttribute('data-from')
  document.getElementById('modal_to').textContent = button.getAttribute('data-to')
  document.getElementById('modal_reason').textContent = button.getAttribute('data-reason')
})
</script>
</body>
</html>
