<!-- MyListings page to display auction item that are on seller created -->
<?php 
  include_once("header.php");
  require("utilities.php");
  include 'database.php'; //Connect to the database
?>

<div class="container">
  <h2 class="my-3">My listings</h2>
  <!-- Check to see if the user is logged in or not -->
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true):?>
    <!-- Filterings that allow seller to re-arrange listings of auction items based on search bar, sortby, and category -->
    <div id="searchSpecs">
      <form method="GET" action="mylistings.php">
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
      session_start(); //Start a session 
      $userId =  $_SESSION['UserId']; //Get session user ID
      $category = $_GET['category']; // Get user's selected cateogry 
      $sortby = $_GET['order_by']; //Get user's selected sort by option
      //If user chose a category 
      if ($category){
        //Query to seller's auction item based on the category he/she chose to display
        $categorySql = "SELECT i.ItemId, ItemName, Description, Cond_, Category, Greatest(StartingBid, COALESCE(b.HighestBid,0)) as Price, AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount, f.ImageLocation
        FROM Item i
        LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
        FROM `BidRecord` 
        GROUP BY ItemId) b on i.ItemId = b.ItemId
        INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                        FROM Image
                        GROUP BY ItemId)) f on i.ItemId = f.ItemId
        WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP 
        AND UserId = $userId 
        AND Category = '$category';";
        $categoryresult = mysqli_query($conn, $categorySql);
        if ($categoryresult->num_rows > 0){
          while($row = mysqli_fetch_assoc($categoryresult)) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $desc = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_mylisting_li function defined in utilities.php
            print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time,$image,$time_remaining);
          };
        }else{
          echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
          <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
        }
      }else if ($sortby){ //If user chose a sort by option 
        //Sort item by price from highest to lowest
        if($sortby=='pricehigh'){ 
          //Query to get seller's auction item and order from highest price to lowest
          $maxSortSql = "SELECT i.ItemId, ItemName, Description, Cond_, Category, Greatest(StartingBid, COALESCE(b.HighestBid,0)) as Price, AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount, f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                          FROM Image
                          GROUP BY ItemId)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP AND UserId = $userId 
          ORDER BY Price DESC;";  
          $maxSortResult = mysqli_query($conn, $maxSortSql);     
          if ($maxSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($maxSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['Price'];
              $num_bids = $row['BidsCount'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mylisting_li function defined in utilities.php
              print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
          //Sort item by price from lowest to highest
        }else if($sortby=='pricelow'){
          //Query to get seller's auction item and order from lowest price to highest
          $minSortSql = "SELECT i.ItemId, ItemName, Description, Cond_, Category, Greatest(StartingBid, COALESCE(b.HighestBid,0)) as Price, AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount, f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                          FROM Image
                          GROUP BY ItemId)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP AND UserId = $userId 
          ORDER BY Price ASC;";  
          $minSortResult = mysqli_query($conn, $minSortSql);     
          if ($minSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($minSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['Price'];
              $num_bids = $row['BidsCount'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mylisting_li function defined in utilities.php
              print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
          //Sort item by date 
        }else if($sortby=='date'){
          // Query to get seller's auction item and order by expiry date from soonest to latest
          $dateSortSql = "SELECT i.ItemId, ItemName, Description, Cond_, Category, Greatest(StartingBid, COALESCE(b.HighestBid,0)) as Price, AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount, f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                          FROM Image
                          GROUP BY ItemId)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP AND UserId = $userId 
          ORDER BY AuctionEndDateTime ASC;"; 
          $dateSortResult = mysqli_query($conn, $dateSortSql);
          if ($dateSortResult->num_rows > 0){
            while($row = mysqli_fetch_assoc($dateSortResult)) {
              $item_id = $row['ItemId'];
              $title = $row['ItemName'];
              $desc = $row['Description'];
              $price = $row['Price'];
              $num_bids = $row['BidsCount'];
              $end_time = new DateTime($row['AuctionEndDateTime']);
              $image = $row['ImageLocation'];
              // Print out item details using the print_mylisting_li function defined in utilities.php
              print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time,$image,  $time_remaining);
            };
          }else{
            echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
            <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
          }
        }
      }else {
        // Query to get seller's auction item without any filtering
        $sql = "SELECT i.ItemId, ItemName, Description, Cond_, Category, Greatest(StartingBid, COALESCE(b.HighestBid,0)) as Price, AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount, f.ImageLocation
        FROM Item i
        LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
        FROM `BidRecord` 
        GROUP BY ItemId) b on i.ItemId = b.ItemId
        INNER JOIN (SELECT ItemId, ImageLocation FROM Image WHERE ImageId IN (SELECT min(ImageId) 
                        FROM Image
                        GROUP BY ItemId)) f on i.ItemId = f.ItemId         
        WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP 
        AND UserId = $userId;";  
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows > 0){
          while($row = mysqli_fetch_assoc($result)) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $desc = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_mylisting_li function defined in utilities.php
            print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time,$image,  $time_remaining);
          };
        }else{
          echo ('<div class="d-flex justify-content-center"><h3>YOU DO NOT HAVE ANY ITEMS ON YOUR LIST</h3></div>
          <br><div class="d-flex justify-content-center"><p>You will see your submitted auction item once you have submitted the auction form!</p></div>');
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