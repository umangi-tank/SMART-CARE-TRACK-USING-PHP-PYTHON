<?php
session_start();
if(!isset($_SESSION['faculty_id'])) {
    header("Location: faculty_login.php");
    exit();
}
require_once __DIR__ . 'RKU-CAREDESK/db_connect.php';


$announcements = [];
$stmt = $mysqli->prepare("SELECT * FROM announcements WHERE audience IN ('faculty','all') ORDER BY date DESC, time DESC, created_at DESC");
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $announcements[] = $row;
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Announcements - Faculty</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'faculty_sidebar.php'; ?>
<div class="container" style="margin-left:220px; padding:30px;">
    <h2>Announcements for Faculty</h2>
    <?php if (empty($announcements)): ?>
        <div class="alert alert-info">No announcements available.</div>
    <?php else: ?>
        <?php foreach ($announcements as $a): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($a['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars(date('d-m-Y', strtotime($a['date'])) . ' | ' . date('h:i A', strtotime($a['time']))); ?></h6>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($a['message'])); ?></p>
                    <p class="small">From: <?php echo htmlspecialchars($a['from_email']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
