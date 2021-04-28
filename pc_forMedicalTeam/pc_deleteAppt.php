<?php
session_start();
require "pc_login_signup_Function.php";
$db = new pc_login_signup_Function();
//get patient email
if (isset($_GET['patient_email']))
{
    $patient_email = filter_var($_GET['patient_email'], FILTER_SANITIZE_EMAIL); //sanitize email address
    if (filter_var($patient_email, FILTER_VALIDATE_EMAIL)) //validate email address
    
    {
        $id = $db->prepareData($_GET['id']); // get id through query string
        $sql = "DELETE from `appointment`  WHERE `appointment_id` = '$id'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql); // delete query
        if ($result)
        {
            mysqli_close($GLOBALS['mysqli']); // Close connection
            $location = "PatientPage.php?patient_email=" . $patient_email;
            header("location:$location"); // redirects to all records page
            exit;
        }
        else
        {
            echo "Error deleting record"; // display error message if not delete
            
        }
    }
    else
    //patient email invalid
    
    {
        $msg = "Patient email invalid";
        echo $msg;
        return $msg;
    }
}
else
//patient email not retrieved

{
    $msg = "Patient email not retrieved";
    echo $msg;
    return $msg;
}


?>
