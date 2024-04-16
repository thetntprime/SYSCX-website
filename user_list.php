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
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" onclick="return confirm('Are you sure?');">Logout</a></li>
            <li class="navSelect"><a>User List</a></li>
         </ul>
      </nav>
   </div>
   <main>
      <section>
         <h2>User List</h2>
      </section>
   </main>
</body>

</html>

<?php
    include "connection.php";

    if (isset($_SESSION["user_id"])){
        if (isset($_SESSION["completeProfile"])){
            if (isset($_SESSION["admin"])){
                $connect = new mysqli($server_name, $username, $password, $database_name);
      
                if ($connect -> connect_errno) {
                      echo "Error: Couldn't connect " . $connect -> connect_error;
                      exit();
                }
                
                $sql = "SELECT * FROM users_info";
                $send = $connect->prepare($sql);
                $send->execute();
                $result = $send->get_result();

                $allPrograms = array("Computer Systems Engineering", "Software Engineering", "Communications Engineering", "Biomedical and Electrical",
                "Electrical Engineering", "Special");

                echo "<table>
                <tr>
                    <th>student_id</th>
                    <th>first_name</th>
                    <th>last_name</th>
                    <th>student_email</th>
                    <th>program</th>
                    <th>account_type</th>
                </tr>";

                while($row = $result->fetch_assoc()){
                    $sql = "SELECT * FROM users_program WHERE student_id = ?";
                    $send = $connect->prepare($sql);
                    $send->bind_param("i", $row["student_id"]);
                    $send->execute();
                    $res = $send->get_result();
          
                    $program = $res->fetch_assoc();

                    $sql = "SELECT * FROM users_permissions WHERE student_id = ?";
                    $send = $connect->prepare($sql);
                    $send->bind_param("i", $row["student_id"]);
                    $send->execute();
                    $res = $send->get_result();

                    $perm = $res->fetch_assoc();
       
                    for ($i = 0; $i < count($allPrograms); $i++){
                        if ($program["program"] == $allPrograms[$i]){
                            $actualProgram = $allPrograms[$i];
                            echo "<tr>
                            <td>" . $row["student_id"] ."</td>
                            <td>" . $row["first_name"] ."</td>
                            <td>" . $row["last_name"] ."</td>
                            <td>" . $row["student_email"] ."</td>
                            <td>" . $program["program"] ."</td>
                            <td>" . $perm["account_type"] ."</td>
                            </tr>";
                        }
                    }
                }

                echo "</table>";
          
            }
            else{
                echo "<script>window.location.replace('index.php')</script>";
            }
        }
        else{
            echo "<script>window.location.replace('profile.php')</script>";
        }
    }
    else{
        echo "<script>window.location.replace('login.php')</script>";
    }
?>