<?php
require "pc_login_signup_Function.php";

//Add new account detail to database
$db = new pc_login_signup_Function();
$msg = "Default";
if (isset($_POST['email']) && isset($_POST['newPassword']) && isset($_POST['name']))
{ //check if all fields filled
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); //sanitize email address
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) //validate email address
    {
        if ($db->passwordStrength($_POST['newPassword']))
        {
            $sql_checkExisitngAcc = "SELECT * from `medical team` where mt_email ='" . $email . "'"; //check existing account
            $result_checkExistingAcc = mysqli_query($GLOBALS['mysqli'] , $sql_checkExisitngAcc);
            if (mysqli_num_rows($result_checkExistingAcc) != false) //search success && return object
            {
                $row = mysqli_fetch_assoc($result_checkExistingAcc);
                $activated = $row['mt_activated'];
                if ($activated == false)
                { //if account exist && activated
                    $msg = "Account already exist, but not activated yet";
                }
                else
                { //if account exist but not activated
                    $msg = "Account already exist";
                }
            }
            else if ($db->signUp($email, $_POST['newPassword'], $_POST['name']))
            { //proceed to sign up process
                $db->sendVerificationEmail($email);
                $msg = "Sign Up Success";
            }
            else
            {
                $msg = "Sign up Failed";
            } //If sign up failed
        }
        else
        { //if password strength too weak
            $msg = "Password should be at least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.";
        }
    }
    else
    { //if email validation fail
        $msg = "Email is invalid";
    }
}
else
{
    $msg = "All fields are required";
}
?>
<script type="text/javascript">
alert(<?php echo json_encode($msg); ?>);
window.location.href = "./signup.html";
</script>

<?php
mysqli_close($GLOBALS['mysqli']); // Close connection
?>
