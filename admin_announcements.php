<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

require_once(__DIR__ . '/db_connect.php');
require_once(__DIR__ . '/vendor/autoload.php');     // <-- PHPMailer autoload
$mailConfig = require(__DIR__ . '/mail_config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = null;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $from_email = trim($_POST['from_email'] ?? '');
    $audience = $_POST['audience'] ?? 'all';
    $send_email = isset($_POST['send_email']) && $_POST['send_email'] === '1';

    if ($title === '') $errors[] = "Title is required.";
    if ($message === '') $errors[] = "Message is required.";
    if ($date === '') $errors[] = "Date is required.";
    if ($time === '') $errors[] = "Time is required.";
    if ($from_email === '' || !filter_var($from_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (!in_array($audience, ['student','faculty','all'])) $errors[] = "Invalid audience.";

    if (empty($errors)) {
        // Insert into DB
        $stmt = $mysqli->prepare("INSERT INTO announcements (title, message, date, time, from_email, audience) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $title, $message, $date, $time, $from_email, $audience);

        if ($stmt->execute()) {
            $success = "Announcement added successfully.";

            if ($send_email) {
                $recipients = [];

                if ($audience === 'student' || $audience === 'all') {
                    $res = $mysqli->query("SELECT email FROM students WHERE email <> '' AND email IS NOT NULL");
                    while ($row = $res->fetch_assoc()) $recipients[] = $row['email'];
                }
                if ($audience === 'faculty' || $audience === 'all') {
                    $res = $mysqli->query("SELECT email FROM faculty WHERE email <> '' AND email IS NOT NULL");
                    while ($row = $res->fetch_assoc()) $recipients[] = $row['email'];
                }

                $recipients = array_unique($recipients);

                // Prepare mail content
                $mailSubject = "Announcement: " . $title;

                $mailBody  = "Hello,\n\n";
                $mailBody .= $message . "\n\n";
                $mailBody .= "Date: " . date('d-m-Y', strtotime($date)) . "\n";
                $mailBody .= "Time: " . date('h:i A', strtotime($time)) . "\n\n";
                $mailBody .= "From: " . $from_email . "\n\n";
                $mailBody .= "--\nThis is an automated announcement.";

                // ---------- PHPMailer sending ----------
                $sentCount = 0;
                $failed = [];

                $batchSize = 50;
                $batches = array_chunk($recipients, $batchSize);

                foreach ($batches as $batchIndex => $batch) {
                    foreach ($batch as $to) {
                        $mail = new PHPMailer(true);

                        try {
                            $mail->isSMTP();
                            $mail->Host       = $mailConfig['host'];
                            $mail->SMTPAuth   = true;
                            $mail->Username   = $mailConfig['username'];
                            $mail->Password   = $mailConfig['password'];
                            $mail->SMTPSecure = $mailConfig['smtp_secure'];
                            $mail->Port       = $mailConfig['port'];
                            $mail->CharSet    = 'UTF-8';
                            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);

                            $mail->addAddress($to);
                            $mail->addReplyTo($from_email);

                            $mail->isHTML(false);
                            $mail->Subject = $mailSubject;
                            $mail->Body    = $mailBody;

                            $mail->send();
                            $sentCount++;

                            usleep(100000);

                        } catch (Exception $e) {
                            $failed[] = ['email' => $to, 'error' => $mail->ErrorInfo];
                        }
                    }
                    if ($batchIndex !== array_key_last($batches)) {
                        sleep(1);
                    }
                }

                $success .= " Emails attempted: " . count($recipients) . ". Delivered: " . $sentCount . ".";

                if (!empty($failed)) {
                    $success .= " Failures: " . count($failed) . ".";
                    file_put_contents(__DIR__ . '/mail_failures.log',
                        date('Y-m-d H:i:s') . " - " . json_encode($failed) . PHP_EOL,
                        FILE_APPEND
                    );
                }
            }
        } else {
            $errors[] = "DB insert failed: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch announcements
$announcements = [];
$res = $mysqli->query("SELECT * FROM announcements ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) $announcements[] = $row;
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
/* your same CSS preserved */
body {
    font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif;
    background:#f9f9f9;
    margin:0;
    display:flex;
}
.content { margin-left:220px; padding:30px; flex:1; }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.page-header h2 { color:#b71c1c; }
.page-header .welcome { color:#444; font-weight:bold; }
.add-announcement { background:#fff; border-radius:10px; padding:25px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-bottom:40px; }
.add-announcement h3 { color:#b71c1c; margin-bottom:20px; }
.add-announcement input, .add-announcement textarea, .add-announcement select {
    width:100%; padding:12px; margin:10px 0; border-radius:6px; border:1px solid #ccc; font-size:14px;
}
.add-announcement button { background:#b71c1c; color:#fff; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-size:14px; }
.add-announcement button:hover { background:#880e4f; }
.announcements-table { background:#fff; border-radius:10px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow-x:auto; }
.announcements-table table { width:100%; border-collapse:collapse; }
.announcements-table th, .announcements-table td { padding:12px 15px; border-bottom:1px solid #ddd; text-align:left; font-size:14px; }
.announcements-table th { background:#b71c1c; color:#fff; font-weight:bold; }
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h2>Announcements</h2>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e) echo "<div>" . htmlspecialchars($e) . "</div>"; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="add-announcement">
        <h3><i class="fas fa-plus-circle"></i> Add New Announcement</h3>
        <form method="post" novalidate>
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="message" rows="4" placeholder="Message" required></textarea>

            <div class="row">
                <div class="col-md-3"><input type="date" name="date" required></div>
                <div class="col-md-3"><input type="time" name="time" required></div>
                <div class="col-md-6"><input type="email" name="from_email" placeholder="admin@example.com" required></div>
            </div>

            <div class="row">
                <div class="col-md-4"><select name="audience" required>
                    <option value="student">Students</option>
                    <option value="faculty">Faculty</option>
                    <option value="all">All</option>
                </select></div>

                <div class="col-md-4">
                    <input type="checkbox" name="send_email" value="1"> Send email to audience
                </div>

                <div class="col-md-4" style="text-align:right;">
                    <button type="submit"><i class="fas fa-paper-plane"></i> Add Announcement</button>
                </div>
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
                </tr>
            </thead>
            <tbody>
            <?php if (empty($announcements)): ?>
                <tr><td colspan="8">No announcements yet.</td></tr>
            <?php else: $i=1; foreach ($announcements as $row): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= htmlspecialchars($row['title']); ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
                    <td><?= date('d-m-Y', strtotime($row['date'])); ?></td>
                    <td><?= date('h:i A', strtotime($row['time'])); ?></td>
                    <td><?= htmlspecialchars($row['from_email']); ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['audience'])); ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
