<?php 
session_start();
$page_tile = "Registration Form";
include('includes/header.php'); 
include('includes/navbar.php'); 
?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 REGISTER">
                <?php
                    if(isset($_SESSION['status']))
                    {
                        ?>
                        <div class="alert alert-success">
                            <h5><?= $_SESSION['status']; ?></h5>
                        </div>
                        <?php
                        unset($_SESSION['status']);
                    }
                ?>
                <div class="card shadow">
                    <div class="card-header">
                        <h5> Registration Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="code.php" method="POST">
                            <div class="input-group mb-3">
                                <span class="input-group-text">Name</span>
                                <input type="text" name="name" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Phone No</span>
                                <input type="text" name="mobile" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Email</span>
                                <input type="text" name="email" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Password</span>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Confirm Password</span>
                                <input type="password" name="cpassword" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" name="register-btn" class="btn btn-primary">Register Now</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>