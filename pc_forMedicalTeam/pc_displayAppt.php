<?php
session_start();
require "pc_login_signup_Function.php";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Display all records from Database</title>
</head>
<body>

<h2>Record</h2>

<table border="2">
  <tr>
    <td>Appointment ID</td>
    <td>Date</td>
    <td>Time</td>
    <td>Venue</td>
    <td>Purpose</td>
    <td>Remark</td>
    <td>Edit</td>
    <td>Delete</td>
  </tr>

<?php

$db = new pc_login_signup_Function();

if(isset($_GET['patient_email'])){
  $patient_email = $db->prepareData(filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL)); //sanitize email address
$sql = "SELECT `appointment_id`,`date`, `time`, `doctor`, `venue`, `purpose`, `remark`  FROM `appointment` WHERE `user` = '$patient_email';";
$result = mysqli_query($GLOBALS['mysqli'],$sql);
while($data = mysqli_fetch_assoc($result))
{
?>
  <tr>
   <td><?php echo $data['appointment_id']; ?></td>
    <td><?php echo date_format(date_create($data['date']),"Y-m-d"); ?></td>
    <td><?php echo date_format(date_create($data['time']),"H:i"); ?></td>
    <td><?php echo $data['venue']; ?></td>
    <td><?php echo $data['purpose']; ?></td>
    <td><?php echo $data['remark']; ?></td>      
    <td><a href="pc_editAppt.php?id=<?php echo $data['appointment_id'] . "&patient_email=".$patient_email; ?>">Edit</a></td>
    <td><a href="pc_deleteAppt.php?id=<?php echo $data['appointment_id'] . "&patient_email=".$patient_email; ?>">Delete</a></td>
 </tr>
 <?php
}
}
else
{
  $msg = "Error: Patient email not retrieved";
  echo $msg;
  return $msg;
}
?>

</table>

</body>
</html>