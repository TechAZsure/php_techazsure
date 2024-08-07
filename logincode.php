<?php
session_start();
include('dbconn.php');

if(isset($_POST['login_now_btn']))
{
    if(!empty(trim($_POST['email'])) && !empty(trim($_POST['password'])))
    {
        $email = mysqli_real_escape_string($conn,$_POST['email']);
        $password = mysqli_real_escape_string($conn,$_POST['password']);
        $login_query = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
        $login_query_run = mysqli_query($conn, $login_query);

        if(mysqli_num_rows($login_query_run) > 0)
        {
            $row = mysqli_fetch_array($login_query_run);
            if($row['verify_status'] == "1")
            {
                $_SESSION['authenticated'] = TRUE;
                $_SESSION['auth_user'] = [
                    'username' => $row['name'],
                    'mobile' => $row['mobile'],
                    'email' => $row['email'],
                ];
                $_SESSION['status'] = "Your Login Successfully";
                header("Location: dashboard.php");
                exit(0);  
            }
            else
            {
                $_SESSION['status'] = "Please Verify Your Email Address to Login";
                header("Location: login.php");
                exit(0);  
            }
        }
        else
        {
            $_SESSION['status'] = "Invalid Email or Password";
            header("Location: login.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "All fields are Mandetory";
        header("Location: login.php");
        exit(0);
    }
}
?>