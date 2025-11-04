<?php  
session_start();  
if(!isset($_SESSION['admin_name'])) {  
    header("Location: admin_login.php");  
    exit();  
}  
?>  

<!DOCTYPE html>  

<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>CE Admin Timetable</title>  
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">  
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
<style>  
body { font-family:"Gill Sans","Gill Sans MT",Calibri,sans-serif; background:#f9f9f9; margin:0; display:flex; }  
.content { margin-left:220px; padding:30px; flex:1; }  
.dashboard-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }  
.dashboard-header h2 { color:#b71c1c; }  

.select-semester { max-width:200px; margin-bottom:20px; }

.table-bordered { border:2px solid #b71c1c; text-align:center; }
.table-bordered th, .table-bordered td { vertical-align:middle; }
.break-row { background-color:#f2f2f2; font-weight:bold; }

.btn-edit { background:#b71c1c; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:bold; margin-top:15px; cursor:pointer; }
.btn-edit:hover { background:#880e4f; } </style>

</head>  
<body>  

<?php include 'admin_sidebar.php'; ?>  

<div class="content">  
    <div class="dashboard-header">  
        <h2>CE Admin Timetable</h2>  
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>  
    </div>  

<!-- Semester Dropdown -->  
<select class="form-select select-semester">  
    <option selected disabled>Select Semester</option>  
    <option>CE Semester 1</option>  
    <option>CE Semester 2</option>  
    <option>CE Semester 3</option>  
    <option>CE Semester 4</option>  
    <option>CE Semester 5</option>  
    <option>CE Semester 6</option>  
    <option>CE Semester 7</option>  
    <option>CE Semester 8</option>  
</select>  

<!-- Timetable Table -->  
<table class="table table-bordered" id="timetable">  
    <thead class="table-light">  
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
        <tr>  
            <td>08:00 - 09:45</td>  
            <td>Subject 1<br>Subject 2</td>  
            <td>Subject 1<br>Subject 2</td>  
            <td>Subject 1<br>Subject 2</td>  
            <td>Subject 1<br>Subject 2</td>  
            <td>Subject 1<br>Subject 2</td>  
        </tr>  
        <tr class="break-row">  
            <td>09:45 - 10:00 (Tea Break)</td>  
            <td colspan="5">Tea Break</td>  
        </tr>  
        <tr>  
            <td>10:00 - 11:40</td>  
            <td>Subject 3<br>Subject 4</td>  
            <td>Subject 3<br>Subject 4</td>  
            <td>Subject 3<br>Subject 4</td>  
            <td>Subject 3<br>Subject 4</td>  
            <td>Subject 3<br>Subject 4</td>  
        </tr>  
        <tr class="break-row">  
            <td>11:40 - 12:30 (Lunch Break)</td>  
            <td colspan="5">Lunch Break</td>  
        </tr>  
        <tr>  
            <td>12:30 - 14:00</td>  
            <td>Subject 5<br>Subject 6</td>  
            <td>Subject 5<br>Subject 6</td>  
            <td>Subject 5<br>Subject 6</td>  
            <td>Subject 5<br>Subject 6</td>  
            <td>Subject 5<br>Subject 6</td>  
        </tr>  
    </tbody>  
</table>  

<!-- Edit Button -->  
<button class="btn-edit" id="editBtn"><i class="fas fa-edit"></i> Edit Timetable</button>  


</div>  

<script>  
const editBtn = document.getElementById('editBtn');  
let editing = false;  

editBtn.addEventListener('click', () => {  
    const table = document.getElementById('timetable');  
    if(!editing){  
        // Enable editing  
        Array.from(table.rows).forEach((row, i) => {  
            if(i>0){ // skip header  
                Array.from(row.cells).forEach((cell, j) => {  
                    if(!row.classList.contains('break-row') && j>0){ // skip time slot and breaks  
                        cell.contentEditable = "true";  
                        cell.style.background="#fff9f9";  
                    }  
                });  
            }  
        });  
        editBtn.innerHTML = '<i class="fas fa-save"></i> Save Timetable';  
        editing = true;  
    } else {  
        // Disable editing  
        Array.from(table.rows).forEach((row, i) => {  
            if(i>0){  
                Array.from(row.cells).forEach((cell, j) => {  
                    if(!row.classList.contains('break-row') && j>0){  
                        cell.contentEditable = "false";  
                        cell.style.background="";  
                    }  
                });  
            }  
        });  
        editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Timetable';  
        editing = false;  

        // TODO: Save data via PHP/AJAX  
        alert('Timetable changes saved!');  
    }  
});  
</script>  

</body>  
</html>  
