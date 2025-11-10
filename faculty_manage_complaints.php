<?php
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header("Location: faculty_login.php");
    exit();
}

// Sample complaint data (replace later with DB data)
$complaints = [
    ['id'=>1, 'email'=>'john.doe@example.com', 'type'=>'Hostel Issue', 'date'=>'2025-09-01', 'description'=>'Water supply issue in hostel.', 'status'=>'Pending'],
    ['id'=>2, 'email'=>'aditi.mehta@example.com', 'type'=>'Classroom Issue', 'date'=>'2025-09-03', 'description'=>'Projector not working in room 204.', 'status'=>'Resolved'],
    ['id'=>3, 'email'=>'ravi.patel@example.com', 'type'=>'Canteen Issue', 'date'=>'2025-09-05', 'description'=>'Food quality not satisfactory.', 'status'=>'Pending']
];

// Handle reply form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintId = $_POST['complaint_id'];
    $status = $_POST['status'];
    $reply = $_POST['reply'];

    // TODO: Update complaint status and reply in database here

    echo "<script>alert('Reply sent successfully for Complaint ID: $complaintId'); window.location='manage_complaints.php';</script>";
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
body {
    background-color: #f9f9f9;
    font-family: "Gill Sans","Gill Sans MT",Calibri,sans-serif;
}
.content {
    margin-left: 230px;  /* Match sidebar width */
    padding: 30px;
}
.table th, .table td {
    vertical-align: middle;
}
.status-resolved {color: green; font-weight: bold;}
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
        <h3>Manage Complaints</h3>
        <p class="text-muted mb-0"></p>
    </div>
    <div class="card p-4">
        <table class="table table-bordered table-striped text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Complaint Type</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $comp): ?>
                <tr>
                    <td><?php echo $comp['id']; ?></td>
                    <td><?php echo $comp['email']; ?></td>
                    <td><?php echo $comp['type']; ?></td>
                    <td><?php echo $comp['date']; ?></td>
                    <td><?php echo $comp['description']; ?></td>
                    <td>
                        <?php 
                            if ($comp['status'] === 'Resolved') echo "<span class='status-resolved'>Resolved</span>";
                            else echo "<span class='status-pending'>Pending</span>";
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#replyModal" 
                            data-id="<?php echo $comp['id']; ?>" 
                            data-email="<?php echo $comp['email']; ?>" 
                            data-type="<?php echo $comp['type']; ?>" 
                            data-date="<?php echo $comp['date']; ?>" 
                            data-description="<?php echo $comp['description']; ?>">
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
          <h5 class="modal-title" id="replyModalLabel">Reply to Complaint</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="complaint_id" id="complaint_id">
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
                <input type="radio" name="status" value="Pending" class="ms-3" required> Pending
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
// Fill modal with complaint details
const replyModal = document.getElementById('replyModal');
replyModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  document.getElementById('complaint_id').value = button.getAttribute('data-id');
  document.getElementById('modal_email').textContent = button.getAttribute('data-email');
  document.getElementById('modal_type').textContent = button.getAttribute('data-type');
  document.getElementById('modal_date').textContent = button.getAttribute('data-date');
  document.getElementById('modal_description').textContent = button.getAttribute('data-description');
});
</script>

</body>
</html>
