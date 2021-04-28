<?php
session_start();
require_once "/home/hpyzl1jupiter/public_html/medpal-db/config.php";

//function for database
class pc_login_signup_Function
{
    public $data;

    //setup database information
    public function __construct()
    {
        $this->data = null;
    }

    //prep data using escape string to MYSQL prevent Injection
    function prepareData($data)
    {
        return mysqli_real_escape_string($GLOBALS['mysqli'], stripslashes(htmlspecialchars($data)));
    }

    function logIn($email, $password)
    {
        $email = $this->prepareData($email);
        $password = $this->prepareData($password);
        $sql = "SELECT * from   `medical team`   where mt_email = '" . $email . "'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql);
        if (mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_assoc($result);
            $dbemail = $row['mt_email'];
            $dbpassword = $row['mt_password'];
            $dbactivate = $row['mt_activated'];
            if ($dbemail == $email && $dbactivate == false)
            {
                $this->sendVerificationEmail($email);
                $msg = "Account not activated yet.  Please check inbox/spam folder for verification email.";
?>
                
                <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./login.html";
                </script>
                
                <?php
            }
            else if ($dbemail == $email && password_verify($password, $dbpassword) && $dbactivate == true)
            {
                $_SESSION['login_email'] = $email;
                $msg = "Login success";
?>
                
             <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./page1.html";
                </script>
                
                <?php
            }
            else
            {
                $msg = "Email or Password wrong";
?>
                
             <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./login.html";
                </script>
                
                <?php
            }
        }
        else
        {
            $msg = "Email or Password wrong";
?>
                
             <script type="text/javascript">
                    alert(<?php echo json_encode($msg); ?>);
                    window.location.href = "./login.html";
                </script>
                
                <?php
        }
        exit();
    }

    function signUp($email, $password, $name)
    {
        $hashed_password = password_hash($this->prepareData($password) , PASSWORD_BCRYPT); //ENCRYPT PASSWORD WHEN SAVING FROM DATABASE
        $email = $this->prepareData($email);
        $name = $this->prepareData($name);
        $verification_code = $this->prepareData(md5(rand(0, 1000))); //GENERATE RANDOM NO. BETWEEN 0-1000 & HASH
        $sql = "INSERT INTO `medical team` (mt_email,mt_password,mt_verification_code, mt_docName) VALUES ('$email','$hashed_password', '$verification_code', '$name')";
        $result = mysqli_query($GLOBALS['mysqli'], $sql);
        return $result;
    }

    // Validate password strength
    function passwordStrength($password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8)
        {

            return false;
        }
        else return true;
    }

    //Send verification email
    function sendVerificationEmail($email)
    {
        $email = $this->prepareData($email);
        $sql = "SELECT * from   `medical team`   where mt_email = '" . $email . "'";
        $result = mysqli_query($GLOBALS['mysqli'], $sql);
        if (mysqli_num_rows($result) > 0) //if successfully retrieve account detail from database
        
        {
            $row = mysqli_fetch_assoc($result);
            $verification_code = $row['mt_verification_code'];

            //construct email
            //change line 107 if .php file changes in cPanel database
            $subject = 'medPal | Verify your Email';
            $message = '
            Thanks for signing up to medPal!
            
            Please click this link to activate your account:
            https://bulacke.xyz/medpal-db/pc_forMedicalTeam/pc_MTverify.php?mt_email=' . $email . '&mt_verification_code=' . $verification_code . '    

            You may login to your account after activation.';

            $headers = "From: medPal <noreply@medPal.com>\r\n"; // Set from headers
            $headers .= "Reply-To: noreply@medPal.com\r\n";
            $headers .= "Return-Path: noreply@medPal.com\r\n";
            $headers .= "CC: noreply@medPal.com\r\n";
            $headers .= "BCC: noreply@medPal.com\r\n";
            mail($email, $subject, $message, $headers); // Send our email
            return true;
        }
        else return false;
    }
}

?>
