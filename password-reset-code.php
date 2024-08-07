<?php
session_start();
include('dbconn.php');

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_password_reset($get_name, $get_email,$token)
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
        $mail->addAddress($get_email);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification from TechAZsure';
        $email_template = "
            <h2>Hi $get_name<br><br></h2>
            <h3>Your receiving this email because we received a password reset request for your account.</h3>
            <br/><br/>
            <a href='http://localhost/login/password-change.php?token=$token&email=$get_email'>Click Me</a>
            ";
        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }   
}

if(isset($_POST['password_reset_link']))
{
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = md5(rand());

    $check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $check_email_run = mysqli_query($conn, $check_email);

    if(mysqli_num_rows($check_email_run) > 0)
    {
        $row = mysqli_fetch_array($check_email_run);
        $get_name = $row['name'];
        $get_email = $row['email'];

        $update_token = "UPDATE users SET cpassword='$token' WHERE email='$get_email' LIMIT 1";
        $update_token_run = mysqli_query($conn, $update_token);

        if($update_token_run)
        {
            send_password_reset($get_name, $get_email,$token);
            $_SESSION['status'] = "We e-mailed you a password reset link";
            header("Location: password-reset.php");
            exit(0);
        }
        else
        {
            $_SESSION['status'] = "Something went wrong. #1";
            header("Location: password-reset.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "No Email Found";
        header("Location: password-reset.php");
        exit(0);
    }
}

if(isset($_POST['password_update']))
{
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $token = mysqli_real_escape_string($conn, $_POST['password_token']);

    if(!empty($token))
    {
        if(!empty($email) && !empty($new_password) && !empty($confirm_password))
        {
            $check_token = "SELECT cpassword FROM users WHERE cpassword='$token' LIMIT 1";
            $check_token_run = mysqli_query($conn, $check_token);

            if(mysqli_num_rows($check_token_run) > 0)
            {
                if($new_password == $confirm_password)
                {
                    $update_password = "UPDATE users SET password='$new_password' WHERE cpassword='$token' LIMIT 1";
                    $update_password_run = mysqli_query($conn, $update_password);

                    if($update_password_run)
                    {
                        $new_token = md5(rand())."TechAZsure";
                        $update_to_new_token = "UPDATE users SET cpassword='$new_token' WHERE cpassword='$token' LIMIT 1";
                        $update_to_new_token_run = mysqli_query($conn, $update_to_new_token);
                        $_SESSION['status'] = "New Password Successfully Updated";
                        header("Location: login.php");
                        exit(0);
                    }
                    else
                    {
                        $_SESSION['status'] = "Didn't Update Password. Something went wrong.!";
                        header("Location: password-change.php?token=$token&email=$email");
                        exit(0);
                    }
                }
                else
                {
                    $_SESSION['status'] = "Password and Confirm Password Does not Match";
                    header("Location: password-change.php?token=$token&email=$email");
                    exit(0);
                }
            }
            else
            {
                $_SESSION['status'] = "Invalid Token";
                header("Location: password-change.php?token=$token&email=$email");
                exit(0);
            }
        }
        else
        {
            $_SESSION['status'] = "All Fields are Mandetory";
            header("Location: password-change.php?token=$token&email=$email");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "No Token Available";
        header("Location: password-change.php");
        exit(0); 
    }
}

?>