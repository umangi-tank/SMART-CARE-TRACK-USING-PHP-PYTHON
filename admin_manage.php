<?php
session_start();
if(!isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

/*
  admin_manage.php - Single page
  - Option C: Expandable row view
  - All fields editable in expanded edit form
  - Image saved as uploads/<original-sanitized-filename>
  - Old image deleted when replaced / on delete
  - Password: blank = keep old; non-blank -> hashed
*/

// ---------------- Helpers ----------------
function safe_basename($name){
    return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($name));
}
function web_path($path){
    return ltrim(str_replace('\\','/',$path), '/');
}
function move_upload_to_uploads($file_field){
    if(!isset($_FILES[$file_field]) || $_FILES[$file_field]['name'] == '') return null;
    $uploads_dir = __DIR__ . '/uploads/';
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);
    $orig = $_FILES[$file_field]['name'];
    $safe = safe_basename($orig);
    $target_path = $uploads_dir . $safe;
    if(move_uploaded_file($_FILES[$file_field]['tmp_name'], $target_path)){
        return web_path('uploads/' . $safe);
    }
    return null;
}
function unlink_if_exists($relpath){
    if(!$relpath) return;
    $file = __DIR__ . '/' . ltrim($relpath, '/');
    if(file_exists($file) && is_file($file)){
        @unlink($file);
    }
}
function esc($v){ return htmlspecialchars($v, ENT_QUOTES); }

// ---------------- Field lists ----------------
$student_fields = [
    'profile_photo','full_name','marksheet_name','father_name','mother_name','gender','dob','aadhar',
    'blood_group','aadhar_name','email','mobile','category','father_mobile','address1','address2','city',
    'state','country','password','pincode','school','department','program','semester','division','roll_no',
    'admission_no','enrollment_no','admission_year','admission_type','internet_username','internet_password',
    'institute_email','institute_password','apaar_id','anti_ragging'
];

$faculty_fields = [
    'profile_photo','full_name','email','mobile','school','department','program','semester_year',
    'division','class_counsellor','password'
];

$msg = '';

// ---------------- POST handling ----------------
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // ----- Update student -----
    if(isset($_POST['action']) && $_POST['action'] === 'update_student' && isset($_POST['id'])){
        $id = intval($_POST['id']);
        $upload_field = 'student_profile_photo_' . $id;
        $new_photo = move_upload_to_uploads($upload_field);

        $parts = [];
        $params = [];
        $types = '';

        foreach($student_fields as $f){
            if($f === 'profile_photo') continue;
            if($f === 'password'){
                if(isset($_POST['password']) && $_POST['password'] !== ''){
                    $parts[] = "$f = ?";
                    $types .= 's';
                    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                continue;
            }
            if(isset($_POST[$f])){
                $parts[] = "$f = ?";
                $types .= 's';
                $params[] = $_POST[$f];
            }
        }

        if($new_photo !== null){
            $stmt0 = $conn->prepare("SELECT profile_photo FROM students WHERE id = ?");
            $stmt0->bind_param("i",$id);
            $stmt0->execute();
            $old = $stmt0->get_result()->fetch_assoc()['profile_photo'] ?? null;
            unlink_if_exists($old);

            $parts[] = "profile_photo = ?";
            $types .= 's';
            $params[] = $new_photo;
        }

        if(count($parts) > 0){
            $sql = "UPDATE students SET " . implode(", ", $parts) . " WHERE id = ?";
            $types .= 'i';
            $params[] = $id;
            $stmt = $mysqli->prepare($sql);
            if($stmt === false){ $msg = "Prepare failed: ".$conn->error; }
            else {
                $bind = []; $bind[] = $types;
                for($i=0;$i<count($params);$i++) $bind[] = &$params[$i];
                call_user_func_array([$stmt, 'bind_param'], $bind);
                if($stmt->execute()) $msg = "Student updated.";
                else $msg = "Student update failed: ".$stmt->error;
            }
        } else $msg = "No changes for student.";
    }

    // ----- Delete student -----
    if(isset($_POST['action']) && $_POST['action'] === 'delete_student' && isset($_POST['id'])){
        $id = intval($_POST['id']);
        $stmt0 = $conn->prepare("SELECT profile_photo FROM students WHERE id = ?");
        $stmt0->bind_param("i",$id);
        $stmt0->execute();
        $old = $stmt0->get_result()->fetch_assoc()['profile_photo'] ?? null;

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i",$id);
        if($stmt->execute()){
            unlink_if_exists($old);
            $msg = "Student deleted.";
        } else $msg = "Student delete failed: ".$stmt->error;
    }

    // ----- Update faculty -----
    if(isset($_POST['action']) && $_POST['action'] === 'update_faculty' && isset($_POST['id'])){
        $id = intval($_POST['id']);
        $upload_field = 'faculty_profile_photo_' . $id;
        $new_photo = move_upload_to_uploads($upload_field);

        $parts = [];
        $params = [];
        $types = '';

        foreach($faculty_fields as $f){
            if($f === 'profile_photo') continue;
            if($f === 'password'){
                if(isset($_POST['password']) && $_POST['password'] !== ''){
                    $parts[] = "$f = ?";
                    $types .= 's';
                    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                continue;
            }
            if(isset($_POST[$f])){
                $parts[] = "$f = ?";
                $types .= 's';
                $params[] = $_POST[$f];
            }
        }

        if($new_photo !== null){
            $stmt0 = $conn->prepare("SELECT profile_photo FROM faculty WHERE id = ?");
            $stmt0->bind_param("i",$id);
            $stmt0->execute();
            $old = $stmt0->get_result()->fetch_assoc()['profile_photo'] ?? null;
            unlink_if_exists($old);

            $parts[] = "profile_photo = ?";
            $types .= 's';
            $params[] = $new_photo;
        }

        if(count($parts) > 0){
            $sql = "UPDATE faculty SET " . implode(", ", $parts) . " WHERE id = ?";
            $types .= 'i';
            $params[] = $id;
            $stmt = $mysqli->prepare($sql);
            if($stmt === false){ $msg = "Prepare failed: ".$conn->error; }
            else {
                $bind = []; $bind[] = $types;
                for($i=0;$i<count($params);$i++) $bind[] = &$params[$i];
                call_user_func_array([$stmt, 'bind_param'], $bind);
                if($stmt->execute()) $msg = "Faculty updated.";
                else $msg = "Faculty update failed: ".$stmt->error;
            }
        } else $msg = "No changes for faculty.";
    }

    // ----- Delete faculty -----
    if(isset($_POST['action']) && $_POST['action'] === 'delete_faculty' && isset($_POST['id'])){
        $id = intval($_POST['id']);
        $stmt0 = $conn->prepare("SELECT profile_photo FROM faculty WHERE id = ?");
        $stmt0->bind_param("i",$id);
        $stmt0->execute();
        $old = $stmt0->get_result()->fetch_assoc()['profile_photo'] ?? null;

        $stmt = $conn->prepare("DELETE FROM faculty WHERE id = ?");
        $stmt->bind_param("i",$id);
        if($stmt->execute()){
            unlink_if_exists($old);
            $msg = "Faculty deleted.";
        } else $msg = "Faculty delete failed: ".$stmt->error;
    }

    // after any POST, reload lists below (we will fetch fresh later)
}

// ---------------- Fetch lists ----------------
$students = [];
$res = $mysqli->query("SELECT * FROM students ORDER BY id ASC");
if($res && $res->num_rows) while($r = $res->fetch_assoc()) $students[] = $r;

$faculty = [];
$res = $mysqli->query("SELECT * FROM faculty ORDER BY id ASC");
if($res && $res->num_rows) while($r = $res->fetch_assoc()) $faculty[] = $r;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Manage - Students & Faculty</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f6f6;margin:0;display:flex}
.content{margin-left:220px;padding:20px;flex:1}
.table-section{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 2px 6px rgba(0,0,0,0.06)}
img.profile-photo{width:50px;height:50px;object-fit:cover;border-radius:50%}
.btn-edit{background:#52a447;color:#fff;padding:6px 10px;border-radius:6px;border:none}
.btn-delete{background:#8B0000;color:#fff;padding:6px 10px;border-radius:6px;border:none}
.details-box{background:#fafafa;border:1px solid #eee;padding:12px;border-radius:6px;margin-top:8px}
.small-input{font-size:13px;padding:6px}
.form-text{font-size:13px;color:#666}
label.form-label{font-size:13px}
</style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h3 style="color:#b71c1c">Manage Students & Faculty</h3>

    <?php if($msg): ?>
        <div class="alert alert-info"><?php echo esc($msg); ?></div>
    <?php endif; ?>

    <!-- STUDENTS -->
    <div class="table-section">
        <h5><i class="fas fa-user-graduate"></i> Students</h5>
        <div class="table-responsive" style="overflow-x:auto">
            <table class="table table-bordered align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Full Name</th>
                        <th>Marks Name</th>
                        <th>Father</th>
                        <th>Mother</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Aadhar</th>
                        <th>Mobile</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>Semester</th>
                        <th>City</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($students as $i => $s):
                    $id = $s['id'];
                ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><img class="profile-photo" src="<?php echo !empty($s['profile_photo']) ? esc($s['profile_photo']) : 'default-profile.png'; ?>" alt=""></td>
                        <td><?php echo esc($s['full_name']); ?></td>
                        <td><?php echo esc($s['marksheet_name']); ?></td>
                        <td><?php echo esc($s['father_name']); ?></td>
                        <td><?php echo esc($s['mother_name']); ?></td>
                        <td><?php echo esc($s['gender']); ?></td>
                        <td><?php echo esc($s['dob']); ?></td>
                        <td><?php echo esc($s['aadhar']); ?></td>
                        <td><?php echo esc($s['mobile']); ?></td>
                        <td><?php echo esc($s['school']); ?></td>
                        <td><?php echo esc($s['program']); ?></td>
                        <td><?php echo esc($s['semester']); ?></td>
                        <td><?php echo esc($s['city']); ?></td>
                        <td style="white-space:nowrap">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#details-student-<?php echo $id; ?>">View Details</button>
                            <button class="btn-edit btn-sm" onclick="document.getElementById('edit-student-<?php echo $id;?>').style.display='block'; document.getElementById('edit-student-<?php echo $id;?>').scrollIntoView({behavior:'smooth'})">Edit</button>
                            <form method="post" style="display:inline" onsubmit="return confirm('Delete this student?')">
                                <input type="hidden" name="action" value="delete_student">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit" class="btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15" class="p-0">
                            <div id="details-student-<?php echo $id; ?>" class="collapse">
                                <div class="details-box">
                                    <!-- Read-only details -->
                                    <div class="row">
                                        <?php foreach($student_fields as $field):
                                            $val = $s[$field] ?? '';
                                            ?>
                                            <div class="col-md-3 mb-2"><strong><?php echo ucwords(str_replace('_',' ',$field)); ?>:</strong> <?php echo ($field==='profile_photo' && !empty($val)) ? '<a href="'.esc($val).'" target="_blank">View</a>' : esc($val); ?></div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Edit form (hidden by default) -->
                                    <div id="edit-student-<?php echo $id;?>" style="display:none; margin-top:12px; border-top:1px dashed #eee; padding-top:12px;">
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update_student">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">

                                            <div class="row">
                                                <?php
                                                foreach($student_fields as $field):
                                                    $val = $s[$field] ?? '';
                                                    $label = ucwords(str_replace('_',' ',$field));
                                                    if($field === 'profile_photo') continue;
                                                    if($field === 'password'){ ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label"><?php echo $label; ?> (leave blank to keep)</label>
                                                            <input type="password" name="<?php echo $field; ?>" class="form-control small-input" value="">
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label"><?php echo $label; ?></label>
                                                            <input type="text" name="<?php echo $field; ?>" class="form-control small-input" value="<?php echo esc($val); ?>">
                                                        </div>
                                                    <?php }
                                                endforeach;
                                                ?>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Change Profile Photo (optional)</label>
                                                    <input type="file" name="student_profile_photo_<?php echo $id;?>" class="form-control small-input" accept="image/*">
                                                    <div class="form-text">Current: <?php echo esc($s['profile_photo']); ?></div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit-student-<?php echo $id;?>').style.display='none'">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FACULTY -->
    <div class="table-section">
        <h5><i class="fas fa-chalkboard-teacher"></i> Faculty</h5>
        <div class="table-responsive" style="overflow-x:auto">
            <table class="table table-bordered align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Program</th>
                        <th>Sem/Yr</th>
                        <th>Division</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($faculty as $i => $f):
                    $fid = $f['id'];
                ?>
                    <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><img class="profile-photo" src="<?php echo !empty($f['profile_photo']) ? esc($f['profile_photo']) : 'default-profile.png'; ?>" alt=""></td>
                        <td><?php echo esc($f['full_name']); ?></td>
                        <td><?php echo esc($f['email']); ?></td>
                        <td><?php echo esc($f['mobile']); ?></td>
                        <td><?php echo esc($f['school']); ?></td>
                        <td><?php echo esc($f['department']); ?></td>
                        <td><?php echo esc($f['program']); ?></td>
                        <td><?php echo esc($f['semester_year']); ?></td>
                        <td><?php echo esc($f['division']); ?></td>
                        <td style="white-space:nowrap">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#details-fac-<?php echo $fid; ?>">View Details</button>
                            <button class="btn-edit btn-sm" onclick="document.getElementById('edit-fac-<?php echo $fid;?>').style.display='block'; document.getElementById('edit-fac-<?php echo $fid;?>').scrollIntoView({behavior:'smooth'})">Edit</button>
                            <form method="post" style="display:inline" onsubmit="return confirm('Delete this faculty?')">
                                <input type="hidden" name="action" value="delete_faculty">
                                <input type="hidden" name="id" value="<?php echo $fid; ?>">
                                <button type="submit" class="btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" class="p-0">
                            <div id="details-fac-<?php echo $fid; ?>" class="collapse">
                                <div class="details-box">
                                    <div class="row">
                                        <?php foreach($faculty_fields as $field):
                                            $val = $f[$field] ?? '';
                                            ?>
                                            <div class="col-md-3 mb-2"><strong><?php echo ucwords(str_replace('_',' ',$field)); ?>:</strong> <?php echo ($field==='profile_photo' && !empty($val)) ? '<a href="'.esc($val).'" target="_blank">View</a>' : esc($val); ?></div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div id="edit-fac-<?php echo $fid;?>" style="display:none; margin-top:12px; border-top:1px dashed #eee; padding-top:12px;">
                                        <form method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="update_faculty">
                                            <input type="hidden" name="id" value="<?php echo $fid; ?>">

                                            <div class="row">
                                                <?php
                                                foreach($faculty_fields as $field):
                                                    if($field === 'profile_photo') continue;
                                                    $val = $f[$field] ?? '';
                                                    $label = ucwords(str_replace('_',' ',$field));
                                                    if($field === 'password'){ ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label"><?php echo $label; ?> (leave blank to keep)</label>
                                                            <input type="password" name="<?php echo $field; ?>" class="form-control small-input" value="">
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-label"><?php echo $label; ?></label>
                                                            <input type="text" name="<?php echo $field; ?>" class="form-control small-input" value="<?php echo esc($val); ?>">
                                                        </div>
                                                    <?php }
                                                endforeach;
                                                ?>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Change Profile Photo (optional)</label>
                                                    <input type="file" name="faculty_profile_photo_<?php echo $fid;?>" class="form-control small-input" accept="image/*">
                                                    <div class="form-text">Current: <?php echo esc($f['profile_photo']); ?></div>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit-fac-<?php echo $fid;?>').style.display='none'">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
