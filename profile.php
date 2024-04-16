<?php
   session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Update SYSCX profile</title>
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
            <li class="navSelect"><a>Profile</a></li>
            <li><a href="logout.php" onclick="return confirm('Are you sure?');">Logout</a></li>
         </ul>
      </nav>
   </div>

   <main>
      <section>
         <h2>Update Profile information</h2>
         <form method="post" action="">
            <fieldset>
               <legend><span>Personal information</span></legend>
               <p>
                  <label>First Name:</label>
                  <input type="text" name="first_name" id="first_name">
                  <label>Last Name:</label>
                  <input type="text" name="last_name" id="last_name">
                  <label>DOB:</label>
                  <input type="date" name="DOB" id="DOB">
              </p>
            </fieldset>
            <fieldset>
               <legend><span>Address</span></legend>
               <p>
                  <label>Street Number:</label>
                  <input type="text" name="street_number" id="street_number">
                  <label>Street Name:</label>
                  <input type="text" name="street_name" id="street_name">
               </p>
               <p>
                  <label>City:</label>
                  <input type="text" name="city" id="city">
                  <label>Province:</label>
                  <input type="text" name="province" id="province">
                  <label>Postal Code:</label>
                  <input type="text" name="postal_code" id="postal_code">
               </p>
            </fieldset>
            <fieldset>
               <legend><span>Profile Information</span></legend>
               <p>
                   <label>Email address:</label>
                   <input type="text" name="student_email" id="student_email">
               </p>
               <p>
                   <label>Program:</label>
                   <select name="program" id="program">
                      <option value="0">Choose Program</option>
                      <option value="1">Computer Systems Engineering</option>
                      <option value="2">Software Engineering</option>
                      <option value="3">Communications Engineering</option>
                      <option value="4">Biomedical and Electrical</option>
                      <option value="5">Electrical Engineering</option>
                      <option value="6">Special</option>
                   </select>
               </p>
               <p>
                  <label>Choose your Avatar</label><br>
                  <label>
                     <input type="radio" name="avatar" id="0" value="0">
                     <img src="images/img_avatar1.png" alt="avatar 1">
                  </label>
                  <label>
                     <input type="radio" name="avatar" id="1" value="1">
                     <img src="images/img_avatar2.png" alt="avatar 2">
                  </label>
                  <label>
                     <input type="radio" name="avatar" id="2" value="2">
                     <img src="images/img_avatar3.png" alt="avatar 3">
                  </label>
                  <label>
                     <input type="radio" name="avatar" id="3" value="3">
                     <img src="images/img_avatar4.png" alt="avatar 4">
                  </label>
                  <label>
                     <input type="radio" name="avatar" id="4" value="4">
                     <img src="images/img_avatar5.png" alt="avatar 5">
                  </label>
               </p>
               <div>
                <input type="submit" value="Submit" name="profile">
                <input type="reset">
               </div>
             </fieldset>
         </form>
      </section>
   </main>
   <script>
      document.addEventListener('DOMContentLoaded', function(){
         document.getElementsByName("avatar").value = 1;
      });
   </script>
</body>
</html>

<?php

   include "connection.php";

   if (isset($_SESSION["user_id"])){
      $connect = new mysqli($server_name, $username, $password, $database_name);

      if ($connect -> connect_errno) {
		   echo "Error: Couldn't connect " . $connect -> connect_error;
		   exit();
	   }

      $stuID = $_SESSION["user_id"];

      $sql = "SELECT * FROM users_info WHERE student_id = ?";
      $send = $connect->prepare($sql);
      $send->bind_param("i", $stuID);
      $send->execute();
      $result = $send->get_result();

      $user = $result->fetch_assoc();

      $allPrograms = array("Computer Systems Engineering", "Software Engineering", "Communications Engineering", "Biomedical and Electrical",
         "Electrical Engineering", "Special");

      if (isset($_SESSION["admin"])){
         echo "<script>
         document.addEventListener('DOMContentLoaded', function(){
            var list = document.querySelector('ul');
            var newLi = document.createElement('li');
            var link = document.createElement('a');
            link.href = 'user_list.php';
            link.innerHTML = 'User List';
            newLi.appendChild(link);
            list.appendChild(newLi);
         });
         </script>";
      }

      if (isset($_POST["profile"])){

         $email = $_POST["student_email"];
         $fName = $_POST["first_name"];
         $lName = $_POST["last_name"];
         $dob = $_POST["DOB"];
         $checkProgram = $_POST["program"];
         $sNumber = $_POST["street_number"];
         $sName = $_POST["street_name"];
         $city = $_POST["city"];
         $province = $_POST["province"];
         $pCode = $_POST["postal_code"];
   
         $info = array($email, $fName, $lName, $dob, $sNumber, $sName, $city, $province, $pCode);
   
         $empty = false;
      
         foreach($info as $info){
            if(empty($info)){
               $empty = true;
            }
         }

         if ($checkProgram == 0){
            $empty = true;
         }

         if (!$empty){
            $sql = "UPDATE users_info SET student_email = ?, first_name = ?, last_name = ?, dob = ? WHERE student_id = ?";
            $send = $connect->prepare($sql);
            $send->bind_param("ssssi", $_POST["student_email"], $_POST["first_name"], $_POST["last_name"], $_POST["DOB"], $stuID);
            $send->execute();

            $sql = "UPDATE users_avatar SET avatar = ? WHERE student_id = ?";
            $send = $connect->prepare($sql);
            $send->bind_param("ii", $_POST["avatar"], $stuID);
            $send->execute();
      
            $sql = "UPDATE users_address SET street_number = ?, street_name = ?, city = ?, province = ?, postal_code = ? WHERE student_id = ?";
            $send = $connect->prepare($sql);
            $send->bind_param("issssi", $_POST["street_number"], $_POST["street_name"], $_POST["city"], $_POST["province"], $_POST["postal_code"], $stuID);
            $send->execute();

            $actualProgram = $allPrograms[$_POST["program"] - 1];

            $sql = "UPDATE users_program SET program = ? WHERE student_id = ?";
            $send = $connect->prepare($sql);
            $send->bind_param("si", $actualProgram, $stuID);
            $send->execute();

            $_SESSION["completeProfile"] = true;
         }
         else{
            echo "All fields must be completed";
         }
      }

      $sql = "SELECT avatar FROM users_avatar WHERE student_id = ?";
      $send = $connect->prepare($sql);
      $send->bind_param("i", $stuID);
      $send->execute();
      $result = $send->get_result();
      $row = $result->fetch_assoc();

      $checkProfile = $row["avatar"];

      $sql = "SELECT program FROM users_program WHERE student_id = ?";
      $send = $connect->prepare($sql);
      $send->bind_param("i", $stuID);
      $send->execute();
      $result = $send->get_result();

      $program = $result->fetch_assoc();


      if ($checkProfile === NULL){
         //user is a "first time user", just been registered

         $sql = "SELECT * FROM users_info WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();
   
         $user = $result->fetch_assoc();

         $sql = "SELECT program FROM users_program WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();
   
         $program = $result->fetch_assoc();


         for ($i = 0; $i < count($allPrograms); $i++){
            if ($program["program"] == $allPrograms[$i]){
               $actualProgram = $i+1;
            }
         }

         echo "<script>
         document.addEventListener('DOMContentLoaded', function(){
            document.getElementById('first_name').value = '".$user["first_name"]."';
            document.getElementById('last_name').value = '".$user["last_name"]."';
            document.getElementById('DOB').value = '".$user["dob"]."';
            document.getElementById('student_email').value = '".$user["student_email"]."';
            document.getElementById('program').value='".$actualProgram."';
         });
         </script>";
         echo "<div class='rightCol'>
         <p>s</p>
         </div>";//no right profile yet

         echo "Please fill out all fields, no further actions can be done without filling out profile";
      }
      else{
      //has already chosen an avatar, therefore must be returning user

         if(!isset($_SESSION["completeProfile"])){
            $_SESSION["completeProfile"] = true;
         }
         
         $sql = "SELECT * FROM users_info WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();
   
         $user = $result->fetch_assoc();

         $sql = "SELECT program FROM users_program WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();
   
         $program = $result->fetch_assoc();

         for ($i = 0; $i < count($allPrograms); $i++){
            if ($program["program"] == $allPrograms[$i]){
               $actualProgram = $i+1;
            }
         }

         $sql = "SELECT * FROM users_address WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();

         $uAddress = $result->fetch_assoc();

         $sql = "SELECT * FROM users_avatar WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $stuID);
         $send->execute();
         $result = $send->get_result();

         $uAvatar = $result->fetch_assoc();

         echo "<script>
         document.addEventListener('DOMContentLoaded', function(){
            document.getElementById('first_name').value = '".$user["first_name"]."';
            document.getElementById('last_name').value = '".$user["last_name"]."';
            document.getElementById('DOB').value = '".$user["dob"]."';
            document.getElementById('student_email').value = '".$user["student_email"]."';
            document.getElementById('program').value='".$actualProgram."';
            document.getElementById('street_number').value='".$uAddress["street_number"]."';
            document.getElementById('street_name').value='".$uAddress["street_name"]."';
            document.getElementById('city').value='".$uAddress["city"]."';
            document.getElementById('province').value='".$uAddress["province"]."';
            document.getElementById('postal_code').value='".$uAddress["postal_code"]."';
            document.getElementById('".$uAvatar["avatar"]."').checked = true;
         });
         </script>";
         $actualProfileImage = $uAvatar["avatar"]+1;
         echo "<div class='profileCol'>
         <h4>".$user["first_name"]." ".$user["last_name"]."</h4>
         <img id='profileImage' src='images/img_avatar".$actualProfileImage.".png' alt='user avatar'>
         <p>
            Email: <a href='mailto:".$user["student_email"]."'>".$user["student_email"]."</a>
         </p>
         <p>
            Program: ".$allPrograms[$actualProgram - 1]."
         </p>
         </div>";//display current profile
      }

      $send->close();
      $connect->close();
   }
   else{
      echo "<script>window.location.replace('login.php')</script>";
   }

?>