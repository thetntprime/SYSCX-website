<?php
   session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCX</title>
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
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li class="navSelect"><a>Register</a></li>
         </ul>
      </nav>
   </div>
   <main>
      <section>
         <h2>Register a new profile</h2>
         <form method="post" action="">
            <fieldset>
               <legend><span>Personal information</span></legend>
               <p>
                  <label>First Name:</label>
                  <input type="text" name="first_name">
                  <label>Last Name:</label>
                  <input type="text" name="last_name">
                  <label>DOB:</label>
                  <input type="date" name="DOB">
              </p>
            </fieldset>
            <fieldset>
              <legend><span>Profile Information</span></legend>
              <p>
                  <label>Email address:</label>
                  <input type="text" name="student_email">
              </p>
              <p>
                  <label>Program:</label>
                  <select name="program">
                     <option>Choose Program</option>
                     <option>Computer Systems Engineering</option>
                     <option>Software Engineering</option>
                     <option>Communications Engineering</option>
                     <option>Biomedical and Electrical</option>
                     <option>Electrical Engineering</option>
                     <option>Special</option>
                  </select>
              </p>
              <p>
                  <label>Password:</label>
                  <input id="stu_pass" type="password" name="student_password">
                  <label>Confirm Password:</label>
                  <input id="pass_confirm" type="password" name="password_confirm">
                  <p id="passwordIssue">password cannot be blank</p>
              </p>
                  <script>
                     document.addEventListener('DOMContentLoaded', function(){
                        document.getElementById("stu_pass").addEventListener("keyup", testpassword);
                        document.getElementById("pass_confirm").addEventListener("keyup", testpassword);
                        
                        function testpassword(){
                           var password = document.getElementById("stu_pass");
                           var confirm = document.getElementById("pass_confirm");
                           var alert = document.getElementById("passwordIssue");
                           if (password.value == confirm.value && password.value != null && password.value != ""){
                              alert.style.display = "none";
                           }
                           else if (password.value == null || password.value == ""){
                              alert.style.display = "block";
                              alert.innerHTML = "password cannot be blank";
                           }
                           else{
                              alert.style.display = "block";
                              alert.innerHTML = "The confirmation does not match the password";
                           }
                        }
                     });
                  </script>
              <div>
               <input type="submit" value="Submit" name="register">
               <input type="reset">
              </div>
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
      if (isset($_POST["register"])){
         $connect = new mysqli($server_name, $username, $password, $database_name);
   
         if ($connect -> connect_errno) {
            echo "Error: Couldn't connect " . $connect -> connect_error;
            exit();
         }
   
         $sql = "SELECT * FROM users_info";
         $send = $connect->prepare($sql);
         $send->execute();
         $allUsers = $send->get_result();
   
         $email = $_POST["student_email"];
         $fName = $_POST["first_name"];
         $lName = $_POST["last_name"];
         $dob = $_POST["DOB"];
         $pass = $_POST["student_password"];
         $confirm = $_POST["password_confirm"];
   
         $info = array($email, $fName, $lName, $dob, $pass);
   
         $empty = false;
   
         foreach($info as $info){
            if(empty($info)){
               $empty = true;
            }
         }

         $program = $_POST["program"];

         if ($program == "Choose Program"){
            $empty = true;
         }
   
         if (!$empty){
   
            if ($pass == $confirm){
               $existEmail = false;
   
               while($row = $allUsers->fetch_assoc()){
                  if ($row["student_email"] == $email){
                     $existEmail = true;
                  }
               }
   
               if (!$existEmail){
   
                  $encryptPass = password_hash($pass, PASSWORD_BCRYPT);
   
                  $send = $connect->prepare("INSERT INTO users_info (student_id, student_email, first_name, last_name, dob) VALUES (NULL, ?, ?, ?, ?)");
                  $send->bind_param("ssss", $email, $fName, $lName, $dob);
   
                  $send->execute();
   
                  $sql = "SELECT student_id FROM users_info WHERE users_info.student_email = ?";
                  $send = $connect->prepare($sql);
                  $send->bind_param("s", $email);
                  $send->execute();
                  $result = $send->get_result();

                  $row = $result->fetch_assoc();
                  $stuID = $row["student_id"];

                  $_SESSION["user_id"] = $stuID;
                  
                  if($pass == "ADMINISTRATOR"){
                     $sql = "INSERT INTO users_permissions (student_id, account_type) VALUES (?, 0)";
                     $send = $connect->prepare($sql);
                     $send->bind_param("i", $stuID);
                     $send->execute();
                     $_SESSION["admin"] = true;
                  }
                  else{
                     $sql = "INSERT INTO users_permissions (student_id, account_type) VALUES (?, 1)";
                     $send = $connect->prepare($sql);
                     $send->bind_param("i", $stuID);
                     $send->execute();
                  }

                  $send = $connect->prepare("INSERT INTO users_passwords (student_id, password) VALUES (". $stuID .", ?)");
                  $send->bind_param("s", $encryptPass);
   
                  $send->execute();
         
                  $send->prepare("INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code) VALUES (?, NULL, NULL, NULL, NULL, NULL)");
                  $send->bind_param("i", $stuID);

                  $send->execute();
   
                  $sql = "INSERT INTO users_avatar (student_id, avatar) VALUES (?, NULL)";
                  $send = $connect->prepare($sql);
                  $send->bind_param("i", $stuID);
                  $send->execute();
   
                  $send = $connect->prepare("INSERT INTO users_program (student_id, program) VALUES (" . $stuID . ", ?)");
                  $send->bind_param("s", $program);
   
                  $send->execute();
   
                  $send->close();
                  $connect->close();
   
                  echo "<script>window.location.replace('profile.php')</script>";
               }//end email check
               else{
                  echo "This email address already exists, please use a different one";
               }
            }
            else{
               echo "Your password and confirmation do not match";
            }
         }
         else{
            echo "One of the fields were empty. Please try again\n";
         }
      }
   }
   else{
      echo "<script>window.location.replace('profile.php')</script>";
   }
   
?>