<?php
require "pc_login_signup_Function.php";

//login procedure
$db = new pc_login_signup_Function();
if (isset($_POST['email']) && isset($_POST['password']))
{
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); //sanitize email address
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) //validate email address
    
    {
        $db->logIn($_POST['email'], $_POST['password']);
    }
    else echo json_encode("Email is invalid."); //if email validation fail
    
}
else echo json_encode("All fields are required"); //if exist unfilled fields

?>
