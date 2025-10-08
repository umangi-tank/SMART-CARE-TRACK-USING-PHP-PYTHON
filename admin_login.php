<?php
session_start();
$message = "";

// Handle AJAX POST request
if(isset($_POST['image']) && isset($_POST['admin_name'])) {
    $admin_name = preg_replace("/[^a-zA-Z0-9_]/", "", $_POST['admin_name']);
    $folder = "face_data/$admin_name";

    if(is_dir($folder) && count(scandir($folder)) > 2) {
        $_SESSION['admin_name'] = $admin_name;
        echo "success"; // Send success response to JS
    } else {
        echo "error"; // Send error response to JS
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Face Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh; }
.card { border:2px solid #b71c1c; border-radius:10px; padding:30px; background:#fff; width:100%; max-width:500px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
h2 {color:#b71c1c; text-align:center; margin-bottom:20px;}
.btn-danger {background:#b71c1c; border:none; width:100%; margin-top:10px;}
.btn-danger:hover {background:#880e4f;}
#video { width:100%; border-radius:8px; border:1px solid #b71c1c; }
.message {margin-top:15px; text-align:center; color:#b71c1c; font-weight:bold;}
</style>
</head>
<body>

<div class="card">
    <h2>Admin Face Login</h2>
    <input type="text" id="admin_name" class="form-control mb-3" placeholder="Enter Admin Name">

    <video id="video" autoplay></video>
    <canvas id="canvas" style="display:none;"></canvas>

    <button class="btn btn-danger" onclick="capture()">Login with Face</button>
    <div class="message" id="msg"></div>
</div>

<script>
const video = document.getElementById('video');
navigator.mediaDevices.getUserMedia({video:true})
.then(stream => { video.srcObject = stream; })
.catch(err => { alert("Camera access denied!"); });

function capture() {
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/png');

    const admin_name = document.getElementById('admin_name').value.trim();
    if(admin_name=="") { alert("Enter admin name"); return; }

    const formData = new FormData();
    formData.append('image', dataURL);
    formData.append('admin_name', admin_name);

    fetch('admin_login.php', {method:'POST', body:formData})
    .then(response => response.text())
    .then(data => {
        if(data === "success") {
            window.location.href = "admin_dashboard.php"; // Directly navigate to dashboard
        } else {
            document.getElementById('msg').innerText = "Face not recognized. Please register first!";
        }
    })
    .catch(err => { alert("Error sending image"); });
}
</script>

</body>
</html>
