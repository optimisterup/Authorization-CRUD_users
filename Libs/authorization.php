<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$login = $password = "";
$login_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["login"]))){
        $login_err = 'Please enter login.';
    } else{
        $login = trim($_POST["login"]);
    }
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

    if(empty($login_err) && empty($password_err)){
        $sql = "SELECT login, password, admin FROM users WHERE login = :login";
        if($stmt = $pdo->prepare($sql)){
            $param_login = trim($_POST["login"]);
            $stmt->bindParam(':login', $param_login, PDO::PARAM_STR);
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if login exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $hashed_password = $row['password'];
                        if ($row['admin']!=0 ) {
                            if(password_verify($password, $hashed_password) && $row['admin']==1 ){
                                /* Password is correct, so start a new session and
                                save the login to the session */
                                session_start();
                                $_SESSION['login'] = $login;
                                header("location: /index.php");
                            } else{
                                // Display an error message if password is not valid
                                $password_err = 'The password you entered was not valid.';
                            }
                        }else{
                            $password_err = 'You do not have access rights';
                        }
                    }
                } else{
                    // Display an error message if login doesn't exist
                    $login_err = 'No account found with that login.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
    }
    // Close connection
    unset($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($login_err)) ? 'has-error' : ''; ?>">
            <label>login</label>
            <input type="text" name="login" class="form-control" value="<?php echo $login; ?>">
            <span class="help-block"><?php echo $login_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>
    </form>
</div>
</body>
</html>