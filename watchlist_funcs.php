<?php
  include 'database.php'; //Connect to the database
  //Get current session 
  session_start(); 
  $user_id = $_SESSION['UserId']; 
  $item_id = $_SESSION['ItemId']; 
  $functionname = $_SESSION['functionname']; //Get session function name from 

  // If $functionname is add_to_wathclist (i.e., item is not on user's watchlist)
  if ($functionname=="add_to_watchlist"){
    //Query to insert the item into user's watchlist 
    $sql = "INSERT INTO WatchList (WatchListId, UserId, ItemId) VALUES (NULL, '$user_id', '$item_id')";
    $run = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    header('Location: ' . $_SERVER['HTTP_REFERER']); //Auto refresh back to listing.php of the item
    //If $functionname is remove_to_wathclist (i.e., item is already on user's watchlist)
  }else if ($functionname=="remove_to_watchlist"){
    //Query to remove the item from user's watchlist
    mysqli_query($conn,"DELETE FROM WatchList WHERE UserId = '$user_id' AND ItemId = '$item_id'");
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  $conn->close(); //Close connection
?>
