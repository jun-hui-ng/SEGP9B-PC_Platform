<?php 
session_start();
require_once "pc_login_signup_Function.php";
?>
<!doctype html>
<head>
<link rel ="stylesheet" href="displayPatient.css">
<title>Display Patient</title>
</head>
 <body>



<!--pop up form-->
<input type="checkbox" id="show">
<label for="show" class="show-btn">Add Patient</label>

<div class = "accessform">
		<label for= "show" class ="close-btn" title="close">&times;</label>
		<form method="POST" action="pc_addPatient.php"> 
Email: <input type="email" id="patient_email" name="patient_email" required >
<br><br>
  <input type="submit" name="submit" value="Submit">  
</form>
</div>
<h1> Patient </h1>
        
<table border="2">
  <tr>
    <td>Patient Name</td>
    <td>Patient Email</td>
    <td></td>
  </tr>

<?php
if (isset($_SESSION['login_email']))
{
    $mt_email = $_SESSION['login_email'];
    $sql = "SELECT `patient_email`  FROM `medicalTeam_access_patient` WHERE `mt_email` LIKE '$mt_email' AND `permission_activated` = 1;";
    $result = mysqli_query($GLOBALS['mysqli'], $sql);
    $counter = 1;
    while ($data = mysqli_fetch_assoc($result))
    {
?>
  <tr>
   <td><?php echo "Patient " . $counter ?></td>
    <td><?php echo $data['patient_email']; ?></td>
    <td><a href="PatientPage.php?&patient_email=<?php echo $data['patient_email']; ?>">View Appointment</a></td>
 <?php
        $counter++;
    }
}
else
{
    $msg = "Session timeout";
    echo $msg . ". Please login again";
    return $msg;
}
?>
?>

</table>

</body>