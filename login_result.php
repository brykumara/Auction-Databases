<?php
  include_once("database.php"); //Connect to the database
  //When User submit login form in Login.php
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Removes any special characters from email and password that may interfere with the query operations
    $email = mysqli_real_escape_string($conn,$_POST['Email']); 
    $password = mysqli_real_escape_string($conn,$_POST['Password']); 
    //Hash password
    $password = md5($password); 
    //Queyr to get UserId based on user login form entry
    $sql = "SELECT UserId FROM User WHERE Email = '$email' and Password = '$password'";
    $result = mysqli_query($conn,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);
    // If result matched email and password, table row must be 1 row
    if($count == 1) {
      session_start(); //start a session
      //Set session variables
      $_SESSION['logged_in'] = true; 
      $_SESSION['Email'] = $email;
      $userId = current($conn->query("SELECT UserId FROM User WHERE Email = '$email'")->fetch_assoc());
      $_SESSION['UserId'] = $userId; 
      header("refresh:0.5;url= browse.php"); //Auto refresh to browse.php
    }else { //If no user found
      echo '<script>alert("Wrong username/password combination")</script>';
      $_SESSION['logged_in'] = false; //Set session variable logged_in as false, so user stay logged out and can't access content of the rest of the site
      header("refresh:0.5; url=login.php"); //Auto refresh back to login.php
    }
  }
  //https://codewithawa.com/posts/complete-user-registration-system-using-php-and-mysql-database
  //have a look at 
  //https://www.tutorialspoint.com/php/php_mysql_login.htm
  $conn->close();
?>