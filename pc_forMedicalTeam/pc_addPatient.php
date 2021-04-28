<?php 
session_start();
require_once "pc_login_signup_Function.php";



$db = new pc_login_signup_Function();

//get doctor email
if (isset($_SESSION['login_email']))
{
    $mt_email = $_SESSION['login_email'];

//get patient email
if (isset($_POST['patient_email']))
{
    $patient_email = $db->prepareData(filter_var($_POST['patient_email'], FILTER_SANITIZE_EMAIL)); //sanitize email address
    if (filter_var($patient_email, FILTER_VALIDATE_EMAIL)) //validate email address
    
    {
        //if patient email valid
        //check if patient email is in database && activated &
        $sql = "SELECT * FROM `user account` WHERE email = '$patient_email'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql);
        if (mysqli_num_rows($result) != 0) //check if patient account exist
        
        {
            $row = mysqli_fetch_assoc($result);
            $dbactivate = $row['activated'];
            if ($dbactivate == true) //check if patient account is activated
            
            { //check if relationship exist in database
                $sql = "SELECT *  FROM `medicalTeam_access_patient` WHERE `mt_email` LIKE '$mt_email' AND `patient_email` LIKE '$patient_email';";
                $result = mysqli_query($GLOBALS['mysqli'], $sql);
                if (mysqli_num_rows($result) > 0)
                { //if relationship exist in database
                    $row = mysqli_fetch_assoc($result);
                    $activated = $row['permission_activated'];
                    if ($activated)
                    {
                        $msg ="Permission granted, this account have access to this patient's data.";
                    }
                    else
                    {
                        sendMTrequestPermissionEmail($mt_email, $patient_email);
                        $msg = "Email to request permission from patient sent.";
                    }

                }
                else
                {
                    //set relationship in database
                    $sql = "INSERT INTO `medicalTeam_access_patient` (`mt_email`, `patient_email`, `permission_activated`) VALUES ('$mt_email', '$patient_email', '0');";
                    $result = mysqli_query($GLOBALS['mysqli'], $sql);
                    //send request permission mail
                    sendMTrequestPermissionEmail($mt_email, $patient_email);
                    $msg = "Email to request permission from patient sent.";
                }
            }
            else
            {
                //patient email not found in database
                $msg = "Patient account not activated yet.";
            }
        }
        else
        { //patient email not found in database
            $msg = "Patient account not found.";
        }
    }
    else
    {
        //if email invalid
        $msg = "Invalid email";
    }
}
else
{
    //if patient email not provided
    $msg = "Please enter patient email to request access".$_POST['patient_email'];
}
}
else
{
    $msg = "Error: Doctor email not found. Please login again.";
}
?>
<script type="text/javascript">
alert(<?php echo json_encode($msg); ?>);
window.location.href = "./displayPatient.php";
</script>

<?php
mysqli_close($GLOBALS['mysqli']); // Close connection
?>

<?php
//Send request permission email
function sendMTrequestPermissionEmail($mt_email, $patient_email)
{
    $db = new pc_login_signup_Function();

    if (isset($mt_email))
    { //check if doctor email received
        //search for doctor name
        $sql_doctorName = "SELECT * FROM `medical team` WHERE `mt_email` LIKE '$mt_email'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql_doctorName);
        $row = mysqli_fetch_assoc($result);
        $mt_docName = $row['mt_docName'];

        $mt_email = $db->prepareData($mt_email);
        $patient_email = $db->prepareData($patient_email);
        $sql = "SELECT * FROM `medicalTeam_access_patient` WHERE `mt_email` LIKE '$mt_email' AND `patient_email` LIKE '$patient_email'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql);
        if (mysqli_num_rows($result) > 0) //if successfully retrieve relationship detail from database
        
        {
            //construct email
            //change line 107 if .php file changes in cPanel database
            $subject = 'medPal | ' . 'Doctor ' . $mt_docName . ' requests permission to access your account';
            $message = '
         Doctor ' . $mt_docName . ' has sent a request to access your account. 
         
         By approving request permission, you have granted Doctor ' . $mt_docName . ' the permission to view/create/edit/delete your records including appointment and medication list. 
         To approve request & grant access, please click onto the link below:
         http://www.hpyzl1.jupiter.nottingham.edu.my/medpal-db/pc_forMedicalTeam/pc_requestPermissionSuccess.php?mt_email=' . $mt_email . '&patient_email=' . $patient_email . '    

         If you do not want to approve the following request, please ignore this email and DO NOT CLICK INTO LINK. ';

            $headers = "From: medPal <noreply@medPal.com>\r\n"; // Set from headers
            $headers .= "Reply-To: noreply@medPal.com\r\n";
            $headers .= "Return-Path: noreply@medPal.com\r\n";
            $headers .= "CC: noreply@medPal.com\r\n";
            $headers .= "BCC: noreply@medPal.com\r\n";
            mail($patient_email, $subject, $message, $headers); // Send our email
            return true;
        }
        else return false;
    }

}
?>
