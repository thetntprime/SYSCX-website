<?php
   session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>SYSCX - Login</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <div class="firstCol">
      <nav>
         <ul class="navBar">
            <li class="navSelect"><a>Login</a></li>
         </ul>
      </nav>
   </div>
    <main>
        <section>
            <form method="post" action="">
                <fieldset>
                    <legend><span>Login</span></legend>
                    <p>
                        <label>Email</label>
                        <input type="text" name="email">
                        <label>Password:</label>
                        <input type="password" name="password">
                    </p>
                    <div>
                        <input type="submit" value="Login" name="login">
                    </div>
                    <p>Don't have an account? <a href="register.php">Click to register</a></p>
                </fieldset>
            </form>
        </section>
    </main>
    <div class="rightCol">
        <p>s</p>
    </div>
</body>

</html>

<?php
    include "connection.php";

    if (!isset($_SESSION["user_id"])){

        if (isset($_POST["login"])){
            $connect = new mysqli($server_name, $username, $password, $database_name);
      
            if ($connect -> connect_errno) {
                  echo "Error: Couldn't connect " . $connect -> connect_error;
                  exit();
            }
            
           // try{
                $email = $_POST["email"];
                $password = $_POST["password"];
        
                $sql = "SELECT student_id FROM users_info WHERE student_email = ?";
                $send = $connect->prepare($sql);
                $send->bind_param("s", $email);
                $send->execute();
                $result = $send->get_result();

                if ($result->num_rows > 0){
                    $user = $result->fetch_assoc();
                    $uid = $user["student_id"];
                    $sql = "SELECT password FROM users_passwords WHERE student_id = ?";
                    $send = $connect->prepare($sql);
                    $send->bind_param("i", $uid);
                    $send->execute();
                    $result = $send->get_result();
    
                    $row = $result->fetch_assoc();
                    $encrypted = $row["password"];
                    if (password_verify($password, $encrypted)){
                        $_SESSION["user_id"] = $uid;
    
                        $sql = "SELECT avatar FROM users_avatar WHERE student_id = ?";
                        $send = $connect->prepare($sql);
                        $send->bind_param("i", $uid);
                        $send->execute();
                        $result = $send->get_result();
    
                        $row = $result->fetch_assoc();

                        $sql = "SELECT account_type FROM users_permissions WHERE student_id = ?";
                        $send = $connect->prepare($sql);
                        $send->bind_param("i", $uid);
                        $send->execute();
                        $result = $send->get_result();

                        $perm = $result->fetch_assoc();
    
                        $send->close();
                        $connect->close();

                        if($perm["account_type"] == 0){
                            $_SESSION["admin"] = true;
                        }
    
                        if ($row["avatar"] != NULL){
                            $_SESSION["completeProfile"] = true;
    
                            echo "<script>window.location.replace('index.php')</script>";
                        }
                        else{
                            echo "<script>window.location.replace('profile.php')</script>";
                        }
                    }
                    else{
                        echo "The password was incorrect";
                    }
                }
                else{
                    echo "login failed: an issue occured with getting the user";
                }
            //}
           // catch(Exception $e){
            //    echo " error 1 ";
            //}
        }
    }
    else{
        if (isset($_SESSION["completProfile"])){
            echo "<script>window.location.replace('index.php')</script>";
        }
        else{
            echo "<script>window.location.replace('profile.php')</script>";
        }
    }
    
?>