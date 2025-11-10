<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

// Sample leave requests (replace later with DB data)
$leaveApplications = [
    ['id'=>1, 'student'=>'John Doe', 'type'=>'Sick Leave', 'from'=>'2025-09-01', 'to'=>'2025-09-02', 'reason'=>'Fever and rest recommended by doctor.', 'status'=>'Pending'],
    ['id'=>2, 'student'=>'Aditi Mehta', 'type'=>'Casual Leave', 'from'=>'2025-09-05', 'to'=>'2025-09-05', 'reason'=>'Family function.', 'status'=>'Pending'],
    ['id'=>3, 'student'=>'Ravi Patel', 'type'=>'Personal Leave', 'from'=>'2025-09-07', 'to'=>'2025-09-08', 'reason'=>'Personal work.', 'status'=>'Approved']
];

// Handle reply form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leaveId = $_POST['leave_id'];
    $status = $_POST['status'];
    $reply = $_POST['reply'];

    // TODO: Update database with reply and status here

    echo "<script>alert('Reply sent successfully for Leave ID: $leaveId'); window.location='manage_leave_request.php';</script>";
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
body {
    background-color: #f9f9f9;
    font-family: "Gill Sans","Gill Sans MT",Calibri,sans-serif;
}
.content {
    margin-left: 230px;  /* Match your sidebar width */
    padding: 30px;
}
.table th, .table td {
    vertical-align: middle;
}
.status-approved {color: green; font-weight: bold;}
.status-rejected {color: red; font-weight: bold;}
.status-pending {color: orange; font-weight: bold;}
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.page-header h3 {
    color: #b71c1c;
    font-weight: bold;
}
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.modal-header {
    background-color: #b71c1c;
}
.modal-header h5 {
    color: white;
}
.btn-outline-primary {
    border-color: #b71c1c;
    color: #b71c1c;
}
.btn-outline-primary:hover {
    background-color: #b71c1c;
    color: white;
}
</style>
</head>
<body>

<?php include "faculty_sidebar.php"; ?>

<div class="content">
    <div class="page-header">
        <h3>Manage Leave Requests</h3>
        <p class="text-muted mb-0"></p>
    </div>

    <div class="card p-4">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Type</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaveApplications as $req): ?>
                <tr>
                    <td><?php echo $req['id']; ?></td>
                    <td><?php echo $req['student']; ?></td>
                    <td><?php echo $req['type']; ?></td>
                    <td><?php echo $req['from']; ?></td>
                    <td><?php echo $req['to']; ?></td>
                    <td><?php echo $req['reason']; ?></td>
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
                            data-id="<?php echo $req['id']; ?>" 
                            data-student="<?php echo $req['student']; ?>" 
                            data-type="<?php echo $req['type']; ?>" 
                            data-from="<?php echo $req['from']; ?>" 
                            data-to="<?php echo $req['to']; ?>" 
                            data-reason="<?php echo $req['reason']; ?>">
                            <i class="bi bi-reply-fill"></i> Reply
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
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
// Fill modal with clicked leave details
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
