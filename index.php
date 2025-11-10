<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RKU CAREDESK</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Final CSS Code - Red, White, Black, Gray theme with Gill Sans */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: "Gill Sans","Gill Sans MT",Calibri,sans-serif;
      background: #f9f9f9; /* Light Gray/White */
      color: #333; /* Black/Dark Gray */
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Header Styling */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 50px;
      background: #b71c1c; /* Dark Red */
      color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    header h1 {
      margin: 0;
      font-size: 28px;
      letter-spacing: 1px;
    }
    header a {
      text-decoration: none;
      background: white;
      color: #b71c1c;
      padding: 8px 18px;
      border-radius: 5px;
      font-weight: bold;
      transition: 0.3s;
      margin-left: 10px;
    }
    header a:hover {
      background: #880e4f; /* Darker hover red */
      color: white;
    }

    /* Main Content */
    main {
      flex: 1;
      text-align: center;
      padding: 60px 20px;
    }
    main h2 {
      font-size: 36px;
      margin-bottom: 40px;
      color: #b71c1c;
    }

    /* Features Grid */
    .features {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      max-width: 1100px;
      margin: auto;
    }
    @media (max-width: 900px) {
      .features {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 600px) {
      .features {
        grid-template-columns: 1fr;
      }
    }

    .feature-box {
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 25px;
      transition: 0.3s;
      border-top: 5px solid #b71c1c;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .feature-box:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .feature-box i {
      font-size: 50px;
      color: #b71c1c;
      margin-bottom: 15px;
    }
    .feature-box h3 {
      color: #b71c1c;
      margin-bottom: 10px;
      text-align: center;
    }
    .feature-box p {
      color: #555;
      text-align: center;
      font-size: 15px;
    }

    /* Footer Styling */
    footer {
      text-align: center;
      padding: 15px;
      background: #424242; /* Gray */
      color: white;
      margin-top: auto;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
      <h1>RKU SMART CARE TRACK</h1>
      <div>
          <a href="login.php">Students</a>
          <a href="faculty_login.php">Faculty</a>
          <a href="admin_login.php">Admin</a>
      </div>
  </header>

  <main>
    <h2>Welcome to RKU SMART CARE TRACK</h2>
    <H3>Where Attendance and care made Simple</H3>
    <br />

    <div class="features">
      <div class="feature-box">
        <i class="fas fa-calendar-check"></i>
        <h3>Leave Request</h3>
        <p>Submit and track your leave applications seamlessly.</p>
      </div>
      <div class="feature-box">
        <i class="fas fa-envelope"></i>
        <h3>Complaints</h3>
        <p>Raise your concerns and get them resolved effectively.</p>
      </div>
      <div class="feature-box">
        <i class="fas fa-calendar-alt"></i>
        <h3>Timetable Management</h3>
        <p>Manage class schedules and timetable efficiently as admin.</p>
      </div>
      <div class="feature-box">
        <i class="fas fa-user-check"></i>
        <h3>On Face Attendance</h3>
        <p>Mark attendance using face recognition instantly.</p>
      </div>
      <div class="feature-box">
        <i class="fas fa-lock"></i>
        <h3>Admin Login with Face</h3>
        <p>Admin can login securely using face recognition.</p>
      </div>
      <div class="feature-box">
        <i class="fas fa-users-cog"></i>
        <h3>Admin Manage Students & Faculty</h3>
        <p>Manage all students and faculty data efficiently.</p>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
      &copy; <?php echo date("Y"); ?> RKU CAREDESK. All Rights Reserved.
  </footer>

</body>
</html>
