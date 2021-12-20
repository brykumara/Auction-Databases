<?php 
  include_once("header.php");
  include 'database.php'; //Connect to the database
  //When user submit the register form 
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required boxes are NOT empty
    if(!empty($_POST['FirstName']) 
    && !empty($_POST['LastName']) 
    && !empty($_POST['Email']) 
    && !empty($_POST['Password'])) {
      // Receive all input values from "Register Form"	 
      $firstname = $_POST['FirstName'];
      $lastname = $_POST['LastName'];
      $email = $_POST['Email'];
      $password = $_POST['Password'];
      $passwordConfirmation = $_POST['passwordConfirmation'];
      //Query to check if email address already exists in the database 
      $check_email = mysqli_query($conn, "SELECT Email FROM User WHERE Email = '$email'");
      //If If result matched email  table row will be greater than 0
      if(mysqli_num_rows($check_email) != 0 ){ 
        echo '<script>alert("User already exists!")</script>';
        header("refresh:0.5;url= register.php");
      }else{
        $password = md5($password); //Hash password
        //Query to insert new user into database
        $sql = "INSERT INTO `User`(`UserId`, `FirstName`, `LastName`, `Email`,`JoinDate`, `Password`) 
        VALUES (NULL,'$firstname','$lastname','$email',CURRENT_TIMESTAMP,'$password')";
        $run = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $_SESSION['logged_in'] = true;
        $_SESSION['Email'] = $email; //Set session variable Email as user's email address 
        header("refresh:0.5;url= browse.php"); //Auto fresh to browse.php
      }
    }
    mysqli_close($conn);
  }
  ?>
</div>
<?php include_once("footer.php")?>
