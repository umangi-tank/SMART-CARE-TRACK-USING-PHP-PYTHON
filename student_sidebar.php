<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
        margin: 0;
        font-family: "Gill Sans", "Gill Sans MT", Calibri, sans-serif;
        background: #f9f9f9;
    }

    /* Sidebar Design */
    .sidebar {
        width: 240px;
        min-height: 100vh;
        background: #ffffff;
        border-right: 1px solid #ddd;
        padding-top: 20px;
        position: fixed;
        top: 0;
        left: 0;
    }

    /* Branding */
    .sidebar .arc {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }

    .sidebar .brand-rku {
        color: #b71c1c; /* Red */
    }

    .sidebar .brand-soac {
        color: #444; /* Dark Gray */
    }

    /* Links */
    .sidebar a {
        display: block;
        text-align: left;
        padding: 12px 20px;
        color: #333;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 6px;
        margin: 5px 10px;
    }

    /* Icons + Text spacing */
    .sidebar a i {
        margin-right: 10px;
        font-size: 16px;
    }

    /* Hover effect */
    .sidebar a:hover {
        background: #b71c1c;
        color: #fff;
    }

    /* Active link */
    .sidebar a.active {
        background: #b71c1c;
        color: #fff;
        font-weight: bold;
    }

    /* Content Area */
    .content {
        margin-left: 240px;
        padding: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }
        .content {
            margin-left: 200px;
        }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar text-center" id="sidebar">
      <div class="arc text-center">
          <span class="brand-rku"> SOE | </span>
          <span class="brand-soac">CARE DESK</span>
      </div>
      <hr>

        <!-- Admin Sidebar with proper icons -->
    <a href="student_dashboard.php" class="a"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="student_leaveRequest.php" class="a"><i class="bi bi-journal-text"></i> Leave Requests</a>
    <a href="student_complints.php" class="a"><i class="bi bi-exclamation-circle"></i> Complaints Box</a>
    <a href="student_view_attendance.php" class="a"><i class="bi bi-bar-chart-line"></i> View Attendance</a>
    <a href="student_announcements.php" class="a"><i class="bi bi-speaker"></i> Announcements</a>
    <a href="student_timetable.php" class="a"><i class="bi bi-calendar-check"></i> Show Timetable</a>
    <a href="student_profile.php" class="a"><i class="bi bi-person-circle"></i> Profile</a>

    </div>

