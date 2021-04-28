<?php
require "pc_login_signup_Function.php";

//Add new account detail to database
$db = new pc_login_signup_Function();
if (isset($_GET['mt_email']) && isset($_GET['patient_email']))
{ 
    $mt_email = $db->prepareData(filter_var($_GET['mt_email'], FILTER_SANITIZE_EMAIL)); //sanitize doctor email address
    $patient_email = $db->prepareData(filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL)); //sanitize patient email address
    if (filter_var($mt_email, FILTER_VALIDATE_EMAIL) && filter_var($patient_email, FILTER_VALIDATE_EMAIL)) //validate email address
    {
                $sql_setActivated = "UPDATE `medicalTeam_access_patient` SET `permission_activated` = '1' WHERE `medicalTeam_access_patient`.`mt_email` = '$mt_email' AND `medicalTeam_access_patient`.`patient_email` = '$patient_email'";
                $result_setActivated = mysqli_query($GLOBALS['mysqli'] , $sql_setActivated);
                if ($result_setActivated)
                {
                    //search for doctor name
                    $sql_doctorName = "SELECT * FROM `medical team` WHERE `mt_email` LIKE '$mt_email'";
                    $result = mysqli_query($GLOBALS['mysqli'], $sql_doctorName);
                    $row = mysqli_fetch_assoc($result);
                    $mt_docName = $row['mt_docName'];

                    $msg = "Permission granted to access patient account ". $patient_email. " for Doctor " . $mt_docName;
                    echo $msg;
                    return $msg;
                }
                else
                {
                    $msg = "Error. Please ask doctor to request permission again.";
                    echo $msg;
                    return $msg;
                }
            }
            else echo "Patient email invalid";
        }
  
?>
