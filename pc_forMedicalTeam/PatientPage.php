<?php 
session_start();
require_once "pc_login_signup_Function.php";
$db = new pc_login_signup_Function();
$patient_email = $db->prepareData(filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL)); //sanitize email address

?>



<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="PatientPage.css">
  <title>Display Appointment</title>
</head>


<body>
	<input type="checkbox" id="show">
	<label for="show" class="show-btn">add</label>
	<div class = "container">
		<label for= "show" class ="close-btn" title="close">&times;</label>
		<h2>Set new Appointment </h2>
		<form action="<? echo "pc_createAppt.php?patient_email=".$patient_email ?>" method="POST">
  			
			<label>Date:</label>
  			<input type="date" name="date" required><br>

  			<label >Time:</label>
  			<input type="time" name="time" required><br>

			<label >Venue:</label>
  			<input type="text" name="venue" required><br>

  			<label for="lname"> Purpose:</label>
  			<input type="text" name="purpose" required><br>

			<label for="lname"> Remark:</label>
  			<input type="text" name ="remark" required><br>
  			
			<input type="submit" value="Submit">
		</form>
	</div>
	
	
	

	<h1 class = "appointment">Appointment of <?php echo $patient_email?></h1>
	<table class="table" border="2">
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
    <td><a href="pc_deleteAppt.php?id=<?php echo $data['appointment_id'] . "&patient_email=".$patient_email; ?>" onclick="return checkdelete()">Delete</a></td>
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

<script>
    function checkdelete()
    {
	return confirm('Are you sure you want to delete this record?');
     }
</script>


</body>
</html>