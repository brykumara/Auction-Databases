<!-- MyBids page to display item that are on buyer bidded on-->
<?php 
  include_once("header.php");
  require("utilities.php");
?>

<div class="container">
  <h2 class="my-3">My Bids</h2>
  <!-- Check to see if the user is logged in or not -->
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true):?>
    <div id="searchSpecs">
      <form method="GET" action="mybids.php">
        <div class="row">
          <div class="col-md-3 pr-0">
            <div class="form-group">
              <!-- Category drop-down menu -->
              <select class="form-control" name="category">
                <option value="" disabled selected>Select your category</option>
                <option value="Bedroom">Bedroom</option>
                <option value="Living Room">Living Room</option>
                <option value="Kitchen">Kitchen</option>
                <option value="Bathroom">Bathroom</option>
                <option value="Study">Study</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 pr-0">
            <div class="form-inline">
              <!-- Sort by drop-down menu -->
              <select class="form-control" name="order_by">
              <option value="" disabled selected>Sort by:</option>
                <option value='pricelow'>Price (low to high)</option>
                <option value="pricehigh">Price (high to low)</option>
                <option value="date">Soonest expiry</option>
              </select>
            </div>
          </div>
          <div class="col-md-1 px-0">
          <label class="mx-1"> </label>
            <button type="submit" class="btn btn-primary">Sort</button>
          </div>
        </div>
      </form>
    </div> 
</div>

<div class="container mt-5">
  <ul class="list-group">
    <?php
      session_start(); //start a session
      $userId =  $_SESSION['UserId'];
      $category = $_GET['category']; // Get user's selected ategory from the list
      $sortby = $_GET['order_by'];//Get user's selected sort by option
      //If user chose a category
      if ($category){
        //Query to get buyer's bidded item and only display the one matches buyer's selected category 
        $categorySql = "SELECT i.ItemId, i.ItemName, i.Description, b.UserMaxBid,i.AuctionEndDateTime,f.ImageLocation
        FROM Item i 
        INNER JOIN (SELECT ItemId, MAX(BidAmount) AS UserMaxBid 
        FROM `BidRecord` WHERE UserId = '$userId'
        GROUP BY ItemId) b on i.ItemId = b.ItemId 
        INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                      FROM Image
                      GROUP BY ItemId)) f on i.ItemId = f.ItemId
        WHERE Category = '$category';";
        $categoryresult = mysqli_query($conn, $categorySql);
        if ($categoryresult->num_rows > 0){
          while($row = mysqli_fetch_assoc($categoryresult)) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $desc = $row['Description'];
            $price = $row['UserMaxBid'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_mybidding_li function defined in utilities.php
            print_mybidding_li($item_id, $title, $desc, $price, $end_time,$image,  $time_remaining);
          };
        }else{
          echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
          <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
        }
        //If user chose a sort by 
      }else if ($sortby){
        if($sortby=='pricehigh'){ //Sort price from highest to lowest
          // Query to get buyer's bidded item and order by buyer's max bid amount from highest to lowest
          $maxSortSql = "SELECT i.ItemId, i.ItemName, i.Description, b.UserMaxBid,i.AuctionEndDateTime,f.ImageLocation
          FROM Item i 
          INNER JOIN (SELECT ItemId, MAX(BidAmount) AS UserMaxBid 
          FROM `BidRecord` WHERE UserId = '$userId'
          GROUP BY ItemId) b on i.ItemId = b.ItemId 
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                        FROM Image
                        GROUP BY ItemId)) f on i.ItemId = f.ItemId
          ORDER BY UserMaxBid DESC;";  
          $maxSortResult = mysqli_query($conn, $maxSortSql);     
          if ($maxSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($maxSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['UserMaxBid'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mybidding_li function defined in utilities.php
              print_mybidding_li($item_id, $title, $desc, $price, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
        }else if($sortby=='pricelow'){ //Sort by price from lowest to highest 
          // Query to get buyer's bidded item and order by buyer's max bid amount from lowest to highest 
          $minSortSql = "SELECT i.ItemId, i.ItemName, i.Description, b.UserMaxBid,i.AuctionEndDateTime,f.ImageLocation
          FROM Item i 
          INNER JOIN (SELECT ItemId, MAX(BidAmount) AS UserMaxBid 
          FROM `BidRecord` WHERE UserId = '$userId'
          GROUP BY ItemId) b on i.ItemId = b.ItemId 
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                        FROM Image
                        GROUP BY ItemId)) f on i.ItemId = f.ItemId
          ORDER BY UserMaxBid ASC;";  
          $minSortResult = mysqli_query($conn, $minSortSql);     
          if ($minSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($minSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['UserMaxBid'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mybidding_li function defined in utilities.php
              print_mybidding_li($item_id, $title, $desc, $price, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
        }else if($sortby=='date'){ //Sort by date
          //Query to get buyer's bidded items and order by expiry date from soonest to latest
          $dateSortSql = "SELECT i.ItemId, i.ItemName, i.Description, b.UserMaxBid,i.AuctionEndDateTime,f.ImageLocation
          FROM Item i 
          INNER JOIN (SELECT ItemId, MAX(BidAmount) AS UserMaxBid 
          FROM `BidRecord` WHERE UserId = '$userId'
          GROUP BY ItemId) b on i.ItemId = b.ItemId 
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                        FROM Image
                        GROUP BY ItemId)) f on i.ItemId = f.ItemId
          ORDER BY AuctionEndDateTime ASC;"; 
          $dateSortResult = mysqli_query($conn, $dateSortSql);
          if ($dateSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($dateSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['UserMaxBid'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mybidding_li function defined in utilities.php
              print_mybidding_li($item_id, $title, $desc, $price, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
        }
      }else {
        //Query to get buyer's bidded item without any filtering 
        $sql = "SELECT i.ItemId, i.ItemName, i.Description, b.UserMaxBid,i.AuctionEndDateTime,f.ImageLocation
        FROM Item i 
        INNER JOIN (SELECT ItemId, MAX(BidAmount) AS UserMaxBid 
        FROM `BidRecord` WHERE UserId = '$userId'
        GROUP BY ItemId) b on i.ItemId = b.ItemId 
        INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                      FROM Image
                      GROUP BY ItemId)) f on i.ItemId = f.ItemId";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows > 0){
          while($row = mysqli_fetch_assoc($result)) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $desc = $row['Description'];
            $price = $row['UserMaxBid'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_mybidding_li function defined in utilities.php
            print_mybidding_li($item_id, $title, $desc, $price, $end_time,$image,  $time_remaining);
          };
        }else{
          echo ('<div class="d-flex justify-content-center"><h3>YOU DO NOT HAVE ANY BID AT THE MOMENT</h3></div>
            <br><div class="d-flex justify-content-center"><p>You will see your bidded item once you have placed a bid</p></div>');
        }
      }
      $conn->close(); //Close connection 
    ?>
  </ul>
 </div>
  <!-- If user is not logged in, message below will be shown to indicate they need to log in first before access any contents -->
 <?php else :?>
    <p>Please Log in to see the contents :)</p>
 <?php endif?>
<?php include_once("footer.php")?>

