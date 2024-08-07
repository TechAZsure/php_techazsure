<?php
session_start();
include('dbconn.php');

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmail($name,$to, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'connect@techazsure.com'; // Your Gmail email
        $mail->Password = 'TeChAzSuRe786@'; // Your Gmail password or App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('connect@techazsure.com',"TechAZsure");
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification from TechAZsure';
        $email_template = "
            <h2>Hi $name<br><br></h2>
            <h3>You have Registered with TechAZsure</h3>
            <h5>Verify your email address to Login with the below given link</h5>
            <br/><br/>
            <a href='http://localhost/login/verify-email.php?token=$otp'>Click Me</a>
            ";
        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if(isset($_POST['register-btn']))
{
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = md5(rand());

    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($conn, $check_email_query);

    if(mysqli_num_rows($check_email_query_run) > 0)
    {
        $_SESSION['status'] = "Email id is already Exists";
        header('Locatiion: register.php');
    }
    else
    {
        $query = "INSERT INTO users(name,mobile,email,password,cpassword) VALUES('$name','$mobile','$email','$password','$cpassword')";
        $query_run = mysqli_query($conn, $query);

        if($query_run)
        {
            sendEmail("$name","$email","$cpassword");
            $_SESSION['status'] = "Registration Successful.! Please Verify your Email Address";
            header("Location: register.php");
        }
        else
        {
            $_SESSION['status'] = "Registration Failed";
            header("Location: register.php");
        }
    }
}

?>