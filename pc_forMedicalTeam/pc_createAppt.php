<?php
session_start();
require "pc_login_signup_Function.php";

//Add new account detail to database
$db = new pc_login_signup_Function();
//get doctor email
if (isset($_SESSION['login_email']))
{
    $mt_email = $_SESSION['login_email'];
    //get patient email
    if (isset($_GET['patient_email']))
    {

        $patient_email = $db->prepareData(filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL)); //sanitize email address
        if (filter_var($patient_email, FILTER_VALIDATE_EMAIL)) //validate email address
        
        {
            if (isset($_POST['date']) && isset($_POST['time']) && isset($_POST['venue']))
            { //check if all fields filled
                //prep & sanitize data
                $user = $patient_email;
                $date = date_format(date_create($db->prepareData($_POST['date'])),"Ymd");
                $time = date_format(date_create($db->prepareData($_POST['time'])),"Hi");
                $venue = $db->prepareData($_POST['venue']);
                $contact = "0";
                $purpose = $db->prepareData($_POST['purpose']);
                //get Doctor Name
                $sql = "SELECT `mt_docName` FROM `medical team` WHERE `mt_email` LIKE '$mt_email';";
                $result = mysqli_query($GLOBALS['mysqli'], $sql);
                $row = mysqli_fetch_assoc($result);
                $doctor = $row['mt_docName'];
                //doc email get from session variable
                $email = $mt_email;
                $remark = $db->prepareData($_POST['remark']);
                $sql = "INSERT INTO `appointment` (`user`,`date`, `time`, `doctor`, `venue`, `contact`, `email`, `purpose`, `remark`) VALUES ('$patient_email','$date','$time','$doctor','$venue','$contact','$email','$purpose','$remark')";
                $result = mysqli_query($GLOBALS['mysqli'], $sql);
                if ($result != false)
                {
                    $msg = "Appointment created";
                    ?>
                    <script type="text/javascript">
                                alert(<?php echo json_encode($msg); ?>);
                                window.location.href = "<?php echo "./PatientPage.php?patient_email=".$patient_email; ?>";
                            </script>
                            <?php
                }
                else{
                    echo "Fail to create appointment. Return to previous page";
                    ?>
        <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "<?php echo "./PatientPage.php?patient_email=".$patient_email; ?>";
                </script>
                <?php
                }

            }
            else
            {
                $msg = "All fields are required";
                ?>
                <script type="text/javascript">
                            alert(<?php echo json_encode($msg); ?>);
                            window.location.href = "<?php echo "./PatientPage.php?patient_email=".$patient_email; ?>";
                        </script>
                        <?php
            }
        }
        else
        {
            $msg = "Patient email invalid";
                    ?>
        <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./displayPatient.php";
                </script>
                <?php
        }
    }
    else
    {
        $msg = "Patient email not retrived";
        ?>
        <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./displayPatient.php";
                </script>
                <?php
    }

}
else
{
    $msg = "Session timeout.";
    ?>
                <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./login.html";
                </script>
                
        <?php

}
?>

