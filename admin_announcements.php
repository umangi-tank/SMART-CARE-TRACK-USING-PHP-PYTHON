<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

require_once(__DIR__ . '/db_connect.php');

$errors = [];
$success = null;

// Handle Delete
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $mysqli->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if($stmt->execute()) {
        $success = "Announcement deleted successfully.";
    } else {
        $errors[] = "Delete failed: " . $stmt->error;
    }
    $stmt->close();
}

// Old form values
$old = [
    'title' => '',
    'message' => '',
    'date' => '',
    'time' => '',
    'audience' => 'all',
    'from_email' => ''
];

// Handle Edit
if(isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $stmt = $mysqli->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows === 1) {
        $old = $res->fetch_assoc();
        $old['id'] = $edit_id;
    }
    $stmt->close();
}

// Handle form submit (Add / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['title'] = trim($_POST['title'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');
    $old['date'] = $_POST['date'] ?? '';
    $old['time'] = $_POST['time'] ?? '';
    $old['audience'] = $_POST['audience'] ?? 'all';
    $old['from_email'] = trim($_POST['from_email'] ?? '');
    $edit_id = intval($_POST['id'] ?? 0);

    if ($old['title'] === '') $errors[] = "Title is required.";
    if ($old['message'] === '') $errors[] = "Message is required.";
    if ($old['date'] === '') $errors[] = "Date is required.";
    if ($old['time'] === '') $errors[] = "Time is required.";
    if ($old['from_email'] === '') $errors[] = "From Email is required.";
    if (!in_array($old['audience'], ['student','faculty','all'])) $errors[] = "Invalid audience.";

    if(empty($errors)) {
        if($edit_id > 0){
            // Update
            $stmt = $mysqli->prepare("UPDATE announcements SET title=?, message=?, date=?, time=?, audience=?, from_email=? WHERE id=?");
            $stmt->bind_param('ssssssi', $old['title'], $old['message'], $old['date'], $old['time'], $old['audience'], $old['from_email'], $edit_id);
            if($stmt->execute()){
                $success = "Announcement updated successfully.";
                $old = ['title'=>'','message'=>'','date'=>'','time'=>'','audience'=>'all','from_email'=>''];
            } else {
                $errors[] = "Update failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Insert
            $stmt = $mysqli->prepare("INSERT INTO announcements (title,message,date,time,audience,from_email) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $old['title'], $old['message'], $old['date'], $old['time'], $old['audience'], $old['from_email']);
            if($stmt->execute()){
                $success = "Announcement added successfully.";
                $old = ['title'=>'','message'=>'','date'=>'','time'=>'','audience'=>'all','from_email'=>''];
            } else {
                $errors[] = "Insert failed: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch all announcements
$announcements = [];
$res = $mysqli->query("SELECT * FROM announcements ORDER BY created_at DESC");
while($row = $res->fetch_assoc()) $announcements[] = $row;
$res->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Announcements</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; display:flex; }
.content { margin-left:220px; padding:30px; flex:1; }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.page-header h2 { color:#b71c1c; }
.page-header .welcome { color:#444; font-weight:bold; }
.add-announcement { background:#fff; border-radius:10px; padding:25px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-bottom:40px; }
.add-announcement h3 { color:#b71c1c; margin-bottom:20px; }
.add-announcement input, .add-announcement textarea, .add-announcement select { width:100%; padding:12px; margin:10px 0; border-radius:6px; border:1px solid #ccc; font-size:14px; }
.add-announcement button { background:#b71c1c; color:#fff; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-size:14px; }
.add-announcement button:hover { background:#880e4f; }
.announcements-table { background:#fff; border-radius:10px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow-x:auto; }
.announcements-table table { width:100%; border-collapse:collapse; }
.announcements-table th, .announcements-table td { padding:12px 15px; border-bottom:1px solid #ddd; text-align:left; font-size:14px; }
.announcements-table th { background:#b71c1c; color:#fff; font-weight:bold; }
.action-btn { font-size:14px; padding:6px 12px; display:inline-block; width:70px; text-align:center; margin-right:5px; }
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2>Announcements</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? ''); ?></div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e ?? '') . "</div>"; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success ?? ''); ?></div>
    <?php endif; ?>

    <div class="add-announcement">
        <h3><i class="fas fa-plus-circle"></i> <?php echo isset($old['id']) ? 'Edit' : 'Add New'; ?> Announcement</h3>
        <form method="post" novalidate>
            <input type="hidden" name="id" value="<?= intval($old['id'] ?? 0); ?>">
            <input type="text" name="title" placeholder="Title" value="<?= htmlspecialchars($old['title'] ?? ''); ?>" required>
            <textarea name="message" rows="4" placeholder="Message" required><?= htmlspecialchars($old['message'] ?? ''); ?></textarea>

            <div class="row">
                <div class="col-md-3"><input type="date" name="date" value="<?= htmlspecialchars($old['date'] ?? ''); ?>" required></div>
                <div class="col-md-3"><input type="time" name="time" value="<?= htmlspecialchars($old['time'] ?? ''); ?>" required></div>
                <div class="col-md-3"><input type="email" name="from_email" placeholder="from@example.com" value="<?= htmlspecialchars($old['from_email'] ?? ''); ?>" required></div>
                <div class="col-md-3">
                    <select name="audience" required>
                        <option value="student" <?= ($old['audience'] ?? '')=='student'?'selected':'' ?>>Students</option>
                        <option value="faculty" <?= ($old['audience'] ?? '')=='faculty'?'selected':'' ?>>Faculty</option>
                        <option value="all" <?= ($old['audience'] ?? '')=='all'?'selected':'' ?>>All</option>
                    </select>
                </div>
            </div>
            <div style="text-align:right;">
                <button type="submit"><i class="fas fa-paper-plane"></i> <?php echo isset($old['id']) ? 'Update' : 'Add'; ?></button>
            </div>
        </form>
    </div>

    <div class="announcements-table">
        <table>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>From</th>
                    <th>Audience</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(empty($announcements)): ?>
                <tr><td colspan="9">No announcements yet.</td></tr>
            <?php else: $i=1; foreach($announcements as $row): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['title'] ?? ''); ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'] ?? '')); ?></td>
                    <td><?= date('d-m-Y', strtotime($row['date'] ?? '')); ?></td>
                    <td><?= date('h:i A', strtotime($row['time'] ?? '')); ?></td>
                    <td><?= htmlspecialchars($row['from_email'] ?? ''); ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['audience'] ?? '')); ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'] ?? '')); ?></td>
                    <td>
                        <a href="?edit_id=<?= intval($row['id'] ?? 0); ?>" class="btn btn-primary action-btn"><i class="fas fa-edit"></i> Edit</a>
                        <a href="?delete_id=<?= intval($row['id'] ?? 0); ?>" class="btn btn-danger action-btn" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
