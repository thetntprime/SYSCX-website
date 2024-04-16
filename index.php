<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>SYSCX - Main</title>
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
            <li class="navSelect"><a>Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" onclick="return confirm('Are you sure?');">Logout</a></li>
         </ul>
      </nav>
   </div>
   <main>
      <section>
         <h2>New Post</h2>
         <form method="post" action="">
            <fieldset>
               <p>
                  <textarea name="new_post" rows="6" cols="50" placeholder="What is happening!? (max 200 char)"></textarea>
               </p>
               <div>
                  <input type="submit" value="Submit" name="createPost">
                  <input type="reset">
                 </div>
            </fieldset>
         </form>
         <div id="allPosts">
         </div>
      </section>
   </main>
</body>

</html>

<?php
   include "connection.php";

   if (isset($_SESSION["user_id"])){
      if (isset($_SESSION["completeProfile"])){
         $connect = new mysqli($server_name, $username, $password, $database_name);
 
         if ($connect -> connect_errno) {
            echo "Error: Couldn't connect " . $connect -> connect_error;
            exit();
         }
   
         $uid = $_SESSION["user_id"];

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
   
   
         $sql = "SELECT * FROM users_info WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $uid);
         $send->execute();
         $result = $send->get_result();
   
         $user = $result->fetch_assoc();
   
         $allPrograms = array("Computer Systems Engineering", "Software Engineering", "Communications Engineering", "Biomedical and Electrical",
            "Electrical Engineering", "Special");
      
         $sql = "SELECT program FROM users_program WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $uid);
         $send->execute();
         $result = $send->get_result();
      
         $program = $result->fetch_assoc();
   
         for ($i = 0; $i < count($allPrograms); $i++){
            if ($program["program"] == $allPrograms[$i]){
               $actualProgram = $i+1;
            }
         }
   
         $sql = "SELECT * FROM users_avatar WHERE student_id = ?";
         $send = $connect->prepare($sql);
         $send->bind_param("i", $uid);
         $send->execute();
         $result = $send->get_result();
   
         $uAvatar = $result->fetch_assoc();
   
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
   
         if (isset($_POST["createPost"])){
            $t = time();
            $postText = $_POST["new_post"];

            $send = $connect->prepare("INSERT INTO users_posts (post_id, student_id, new_post, post_date) VALUES (NULL, ?, ?, '".date("Y/m/d h:i:s a", $t)."')");
            $send->bind_param("ss", $uid, $postText);
   
            $send->execute();
   
            $sql = "SELECT * FROM users_posts ORDER BY post_id DESC LIMIT 10";
            $send = $connect->prepare($sql);
            $send->execute();
            $result = $send->get_result();
   
            while($posts = $result->fetch_assoc()){

               $sql = "SELECT first_name, last_name FROM users_info WHERE student_id = ?";
               $send = $connect->prepare($sql);
               $send->bind_param("i", $posts["student_id"]);
               $send->execute();
               $poster = $send->get_result();
               $oriPoster = $poster->fetch_assoc();

               echo "<details open class='posts'>
                  <summary>Post by: " . $oriPoster["first_name"] . " " . $oriPoster["last_name"] ." (Posted: ".$posts["post_date"].")</summary>
                  <p>" . $posts["new_post"] . "</p>
               </details>";
            }
         }
         else{
            $sql = "SELECT * FROM users_posts ORDER BY post_id DESC LIMIT 10";
            $send = $connect->prepare($sql);
            $send->execute();
            $result = $send->get_result();
      
            while($posts = $result->fetch_assoc()){
               $sql = "SELECT first_name, last_name FROM users_info WHERE student_id = ?";
               $send = $connect->prepare($sql);
               $send->bind_param("i", $posts["student_id"]);
               $send->execute();
               $poster = $send->get_result();
               $oriPoster = $poster->fetch_assoc();

               echo "<details open class='posts'>
                  <summary>Post by: " . $oriPoster["first_name"] . " " . $oriPoster["last_name"] ." (Posted: ".$posts["post_date"].")</summary>
                  <p>" . $posts["new_post"] . "</p>
               </details>";
            }
         }
      }//end profile check
      else{
         echo "<script>window.location.replace('profile.php')</script>";
      }
   }
   else{
      echo "<script>window.location.replace('login.php')</script>";
   }
?>