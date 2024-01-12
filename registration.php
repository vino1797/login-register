<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: index.php");
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php
        // echo "<pre>";
        //  print_r($_POST);
        //   echo "</pre>";
        //     die;
        
       if(isset($_POST["submit"]))
       { 
            // print_r($_POST);
           

        $fullName=$_POST["fullname"];
        $email=$_POST["email"];
        $password=$_POST["password"];
        $passwordRepeat=$_POST["repeat_password"];

        //  echo "fullname"."$fullName";
        //  echo "<br>";
        //  echo "email"."$email";
        //  echo "<br>";
        //  echo "password"."$password";
        //  echo "<br>";
        //  echo "repeat_password"."$passwordRepeat";
        //  die;


        $passwordHash=password_hash($password, PASSWORD_DEFAULT);
        $errors=array();
        if(empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)){
            array_push($errors,"All fields are required");
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
             array_push($errors,"Email is not Valid");
        }
        if(strlen($password)<8){
             array_push($errors,"Password must be at least 8 charactes long");
        }
        if($password!==$passwordRepeat){
             array_push($errors,"Password does not match");
        }
        require_once "database.php";
        $sql="SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);
        $rowcount = mysqli_num_rows($result);
        if($rowcount>0){
            array_push($errors,"Email already exists!");
        }
        if(count($errors)>0){
            foreach($errors as $error){
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }else{
            require_once "database.php";
            $sql="INSERT INTO users(full_name, email, password)VALUES( ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt=mysqli_stmt_prepare($stmt,$sql);
            if($prepareStmt){
                mysqli_stmt_bind_param($stmt,"sss",$fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
            }else{
                die("Something went wrong");
            }
        }

       }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name :">
            </div>
            <div class="form-group">
                <input type="email"class="form-control" name="email" placeholder="Email :">
            </div>
            <div class="form-group">
                <input type="password"class="form-control" name="password" placeholder="Password :">
            </div>
            <div class="form-group">
                <input type="password"class="form-control" name="repeat_password" placeholder="Repeat Password :">
            </div>
            <div class="form-btn">
                <input type="submit"class="btn btn-primary" name="submit" value="Register">
            </div>
        </form>
        <div><p>Already registered <a href="login.php">Login Here</a></p></div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>