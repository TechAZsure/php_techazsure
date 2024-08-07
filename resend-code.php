<?php
session_start();
include('dbconn.php');

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function resend_email_verify($name, $email, $cpassword)
{
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
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification from TechAZsure';
        $email_template = "
            <h2>Hi $name<br><br></h2>
            <h3>You have Registered with TechAZsure</h3>
            <h5>Verify your email address to Login with the below given link</h5>
            <br/><br/>
            <a href='http://localhost/login/verify-email.php?token=$cpassword'>Click Me</a>
            ";
        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }   
}

if(isset($_POST['resend_email_verify_btn']))
{
    if(!empty(trim($_POST['email'])))
    {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $checkemail_query = "SELECT * FROM USERS WHERE email='$email' LIMIT 1";
        $checkemail_query_run = mysqli_query($conn, $checkemail_query);

        if(mysqli_num_rows($checkemail_query_run) > 0)
        {
            $row = mysqli_fetch_array($checkemail_query_run);
            if($row['verify_status'] == "0")
            {
                $name = $row['name'];
                $email = $row['email'];
                $cpassword = $row['cpassword'];
                resend_email_verify($name,$email,$cpassword);
                $_SESSION['status'] = "Verifcation Email Link has been sent to your email address.!";
                header("Location: login.php");
                exit(0);

            }
            else
            {
                $_SESSION['status'] = "Email already verified. Please Login";
                header("Location: resend-email-verification.php");
                exit(0);
            }
        }
        else
        {
            $_SESSION['status'] = "Email is not registered. Please Register Now";
            header("Location: register.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "Please enter the email field";
        header("Location: resend-email-verification.php");
        exit(0);
    }
}
?>