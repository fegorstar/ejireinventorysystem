<?php

include('class/db.php');

$object = new db();

if($object->is_login())
{
    header('location:index.php');
}

$message = '';

if(isset($_POST["login_button"]))
{
    $formdata = array();

    if(empty($_POST["user_email"]))
    {
        $message .= '<li>Email Address is required</li>';
    }
    else
    {
        if(!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
        {
            $message .= '<li>Invalid Email Address</li>';
        }
        else
        {
            $formdata['user_email'] = trim($_POST["user_email"]);
        }
    }

    if(empty($_POST["user_password"]))
    {
        $message .= '<li>Password is required</li>';
    }
    else
    {
        $formdata['user_password'] = trim($_POST["user_password"]);
    }

    if($message == '')
    {
        $data = array(
            ':user_email'       =>  $formdata['user_email']
        );

        $object->query = "
        SELECT * FROM user_msbs 
        WHERE user_email = :user_email 
        ";

        $object->execute($data);

        if($object->row_count() > 0)
        {
            foreach($object->statement_result() as $row)
            {
                if($row["user_status"] == 'Enable')
                {
                    if($row["user_password"] == $formdata['user_password'])
                    {
                        $_SESSION['user_type'] = $row["user_type"];
                        $_SESSION['user_id'] = $row["user_id"];
                        header('location:index.php');
                    }
                    else
                    {
                        $message = '<li>Wrong Password</li>';
                    }
                }
                else
                {
                    $message = '<li>Your Account has been disabled</li>';
                }
            }
        }
        else
        {
            $message = '<li>Wrong Email Address</li>';
        }

    }

}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login | Ejire Pharmacy Distribution System</title>
        <link href="<?php echo $object->base_url; ?>css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<style>
.blink_me {
    -webkit-animation-name: blinker;
    -webkit-animation-duration: 1s;
    -webkit-animation-timing-function: linear;
    -webkit-animation-iteration-count: infinite;

    -moz-animation-name: blinker;
    -moz-animation-duration: 1s;
    -moz-animation-timing-function: linear;
    -moz-animation-iteration-count: infinite;

    animation-name: blinker;
    animation-duration: 1s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}

@-moz-keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@-webkit-keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}</style>   
 </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                
            
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-6" style="margin-top:100px">

                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                         <div class="card-header bg-white" style="padding:20px">
                       <h2 style=" text-align:center; font-weight:500"><span  style="color:red" class="fa fa-prescription-bottle-alt"></span> <b style="color:black;">Ejire Primary health center</b></h2>
                       <p class="blink_me text-center" style="color:red" >Enter login Details to Login!</p>
  				          
                                 </div>
                                    <div class="card-body">
                                        <?php
                                        if($message != '')
                                        {
                                            echo '<div class="alert alert-danger">'.$message.'</div>';
                                        }
                                        ?>
                                        <form method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="user_email" type="text" name="user_email" placeholder="name@example.com" value="<?php if(isset($_POST['user_email'])) echo $_POST['user_email']; ?>" />
                                                <label for="user_email">Email address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="user_password" type="password" name="user_password" placeholder="Password" value="<?php if(isset($_POST['user_password'])) echo $_POST['user_password']; ?>" />
                                                <label for="user_password">Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <input type="submit"  style="padding:10px"name="login_button" class="btn form-control btn-primary btn-block " value="LOGIN" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="">
<center>                           
 <div class="text-muted text-center">Copyright &copy; Designed by OSHO BOLUWATIFE <?php echo date('Y'); ?></div>
 </center>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo $object->base_url; ?>js/scripts.js"></script>
    </body>
</html>
