<?php
session_start();
require "pc_login_signup_Function.php";

$db = new pc_login_signup_Function();
$id = $db->prepareData($_GET['id']); // get id through query string
$sql = "SELECT * FROM `appointment` WHERE `appointment_id` ='$id'";
$result = mysqli_query($GLOBALS['mysqli'], $sql); // select query
$data = mysqli_fetch_assoc($result); // fetch data
if (isset($_POST['edit'])) // when click on Edit button

{
    //get doctor email
    if (isset($_SESSION['login_email']))
    {
        $mt_email = $_SESSION['login_email'];
        //get patient email
        if (isset($_GET['patient_email']))
        {
            $patient_email = filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL); //sanitize email address
            if (filter_var($patient_email, FILTER_VALIDATE_EMAIL)) //validate email address
            
            {

                //change time format
                $date = date_format(date_create($db->prepareData($_POST['date'])) , "Ymd");
                $time = date_format(date_create($db->prepareData($_POST['time'])) , "Hi");
                $doctor = $db->prepareData($_POST['doctor']);
                $venue = $db->prepareData($_POST['venue']);
                $purpose = $db->prepareData($_POST['purpose']);
                $remark = $db->prepareData($_POST['remark']);

                //check if there is an appointment at that time
                $sql_checkIfApptClash = "SELECT `appointment_id`  FROM `appointment` WHERE `user` LIKE '$patient_email'.com' AND `date` LIKE '$date' AND `time` LIKE '$time' AND `email` LIKE '$mt_email';";
                $result = mysqli_query($GLOBALS['mysqli'], $sql_checkIfApptClash);
                $row = mysqli_fetch_assoc($result);
                $row_count = mysqli_num_rows($result);
                // if more than 1 appt OR 1 appt found but not itself
                if (($row_count == 0) || (($row_count == 1) && ($row['appointment_id'] != $id)))
                {
                    $sql = "UPDATE `appointment` SET date='$date', time='$time', venue='$venue', purpose='$purpose', remark='$remark' WHERE appointment_id='$id'";
                    $edit = mysqli_query($GLOBALS['mysqli'], $sql);

                    if ($edit)
                    {
                        $msg = "Appointment updated successfully";
?>
                    <script type="text/javascript">
                                alert(<?php echo json_encode("Appointment updated successfully"); ?>);
                                window.location.href = "<?php echo "./PatientPage.php?patient_email=" . $patient_email; ?>";
                            </script>
                            <?php
                        exit;
?>
                            <?php
                    }
                    else
                    {
                        $string = mysqli_error();
?>
                    <script type="text/javascript">
                                alert(<?php echo json_encode($msg); ?>);
                                window.location.href = "<?php echo "./PatientPage.php?patient_email=" . $patient_email; ?>";
                            </script>
                            <?php
                    }
                }
                else
                {
                    $msg = "Appointment date & time clashes with another slot. Please choose another date/time.";
?>
                <script type="text/javascript">
                                alert(<?php echo json_encode($msg); ?>);
                                window.location.href = "<?php echo "./PatientPage.php?patient_email=" . $patient_email; ?>";
                            </script>
                            <?php
                }
            }
            //patient_email invalid
            else
            {
                $msg = "Patient email not invalid";
?>
                                  <script type="text/javascript">
                                              alert(<?php echo json_encode($msg); ?>);
                                              window.location.href = "./displayPatient.php";
                                          </script>
                                          <?php
            }
        } //patient email not retrieved
        else
        {
            $msg = "Patient email not retrieved";
?>
            <script type="text/javascript">
                        alert(<?php echo json_encode($msg); ?>);
                        window.location.href = "./displayPatient.php";
                    </script>
                    <?php
        }
    }
    //doctor email not retrieved
    else
    {
        $msg = "Session timeout. Please sign in again.";
        echo $msg;
        return $msg;
?>
        <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./login.html";
                </script>
                <?php
    }
}
?>

<h3>Update Appointment Details</h3>

<form method="POST">
<?php
$id = $db->prepareData($_GET['id']);
$sql = "SELECT * FROM `appointment` WHERE `appointment_id` ='$id'";
$result = mysqli_query($GLOBALS['mysqli'], $sql); // select query
$data = mysqli_fetch_assoc($result); // fetch data

?>
<b>Date : </b> <input type="date" name="date" value= "<?php echo date_format(date_create($data['date']) , "Y-m-d"); ?>" >
<br> <br>
  <b>Time: </b><input type="time" name="time" value= "<?php echo date_format(date_create($data['time']) , "H:i"); ?>" >
  <br> <br>
  <b>Venue: </b><input type="text" name="venue" value =  "<?php echo $data['venue']; ?>">
  <br> <br>
  <b>Purpose: </b><input type="text" name="purpose" value = "<?php echo $data['purpose']; ?>">
  <br> <br>
  <b>Remark: </b><input type="text" name="remark" value = "<?php echo $data['remark']; ?>">
  <br> <br>
  <input type="submit" name="edit" value="Edit">
</form>
