<?php
session_start();
require_once "/home/heibeexi/public_html/medpal-db/config.php";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Display Patient who has Granted Access</title>
</head>
<body>

<h2>Patient</h2>

<table border="2">
  <tr>
    <td>Patient Name</td>
    <td>Patient Email</td>
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
    <td><a href="pc_displayAppt.php?&patient_email=<?php echo $data['patient_email']; ?>">View Appointment</a></td>
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

</table>

</body>
</html>
