<?php
require "pc_login_signup_Function.php";

//Add new account detail to database
$db = new pc_login_signup_Function();
if (isset($_GET['mt_email']) && isset($_GET['mt_verification_code']))
{ //check if all fields filled
    $verification_code = $db -> prepareData($_GET['mt_verification_code']);
    $email = $db->prepareData(filter_var($_GET['mt_email'], FILTER_SANITIZE_EMAIL)); //sanitize email address
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) //validate email address
    {
        $sql_checkExistingAcc = "SELECT * from   `medical team`   where mt_email = '" . $email . "'"; //check existing account
        $result_checkExistingAcc = mysqli_query($GLOBALS['mysqli'], $sql_checkExistingAcc);
        if (mysqli_num_rows($result_checkExistingAcc) != false)
        { //search success and return object
            $dbverification_code = $row['mt_verification_code'];
            if (strcmp($verification_code,$dbverification_code))
            {
                $sql_setActivated = "UPDATE `medical team` SET mt_activated = true WHERE mt_email = '$email'";
                $result_setActivated = mysqli_query($GLOBALS['mysqli'] , $sql_setActivated);
                if ($result_setActivated)
                {
                    echo "Account for " . $email . " has been activated." . nl2br("\n\n") . "Please sign in to <b>medPal</b> PD platform";
                }
                else echo "Account activation for " . $email . " failed." . nl2br("\n\n") . "Please request for activation link from again.";
            }
            else echo "Verification code error";
        }
        else echo "Account not created, cannot be activated. Please sign up again. ";
    }
    else echo "Email is invalid."; //if email validation fail
    
}
else echo "Email/Verification code not received" . $email . $verification_code; //if exist unfilled fields

?>
