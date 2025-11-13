<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php'; // ensure $mysqli is your DB connection variable

// Fetch subjects
$subjects = $mysqli->query("SELECT id, subject_name FROM subject");

// Fetch faculty names (from faculty table)
$faculty_result = $mysqli->query("SELECT full_name FROM faculty");

// Handle timetable form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester = $_POST['semester'];
    $division = $_POST['division'];

    foreach ($_POST['timetable'] as $time_slot => $days) {
        foreach ($days as $day => $values) {
            $subject_id = $values['subject'];
            $faculty_name = $values['faculty'];

            if (!empty($subject_id) && !empty($faculty_name)) {
                // Get subject name
                $subject_res = $mysqli->query("SELECT subject_name FROM subject WHERE id='$subject_id'");
                $subject_name = $subject_res->fetch_assoc()['subject_name'];

                // Insert data
                $stmt = $mysqli->prepare("INSERT INTO timetable (semester, division, day, time_slot, subject, faculty)
                                          VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $semester, $division, $day, $time_slot, $subject_name, $faculty_name);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    echo "<script>alert('✅ Timetable added successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Timetable | Admin</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: "Gill Sans", "Gill Sans MT", Calibri, sans-serif;
    background: #f9f9f9;
    margin: 0;
    display: flex;
}
.content {
    margin-left: 220px;
    padding: 30px;
    flex: 1;
}
h3 {
    color: #b71c1c;
}
.card {
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.table th {
    background-color: #b71c1c;
    color: white;
}
.table td, .table th {
    text-align: center;
    vertical-align: middle;
}
select {
    border-radius: 5px;
}
.btn-primary {
    background-color: #b71c1c;
    border: none;
}
.btn-primary:hover {
    background-color: #9a1717;
}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-calendar-alt me-2"></i>Manage Timetable</h3>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
    </div>

    <div class="card">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Select Semester</label>
                    <select name="semester" class="form-select" required>
                        <option value="">Select Semester</option>
                        <?php for($i=1;$i<=8;$i++): ?>
                            <option value="<?= $i ?>">Sem <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Division</label>
                    <select name="division" class="form-select" required>
                        <option value="">Select Division</option>
                        <?php foreach (['A','B','C','D','E','F','G','H'] as $div): ?>
                            <option value="<?= $div ?>">Div <?= $div ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Time Slot</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $slots = ['8:05-8:55', '8:55-9:45', '10:00-10:50', '10:50-11:40', '12:30-1:20', '1:20-2:10'];
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

                        foreach ($slots as $slot):
                            echo "<tr><td><strong>$slot</strong></td>";
                            foreach ($days as $day):
                                echo "<td>";
                                // Subject dropdown
                                echo "<select name='timetable[$slot][$day][subject]' class='form-select mb-2'>";
                                echo "<option value=''>Select Subject</option>";
                                $subjects->data_seek(0);
                                while ($sub = $subjects->fetch_assoc()) {
                                    echo "<option value='{$sub['id']}'>{$sub['subject_name']}</option>";
                                }
                                echo "</select>";

                                // Faculty dropdown (now uses full_name)
                                echo "<select name='timetable[$slot][$day][faculty]' class='form-select'>";
                                echo "<option value=''>Select Faculty</option>";
                                $faculty_result->data_seek(0);
                                while ($fac = $faculty_result->fetch_assoc()) {
                                    echo "<option value='{$fac['full_name']}'>{$fac['full_name']}</option>";
                                }
                                echo "</select>";

                                echo "</td>";
                            endforeach;
                            echo "</tr>";
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2">
                    <i class="fas fa-save me-2"></i>Save Timetable
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
