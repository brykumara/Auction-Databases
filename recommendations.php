
<?php 
  include_once("header.php");
  require("utilities.php");
?>

<div class="container">
  <h2 class="my-3">Recommendations for you</h2>
  <!-- Check to see if the user is logged in or not -->
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true):?>
    <div id="searchSpecs">
      <!-- Filterings that allow user to re-arrange listings of items based on search bar, sortby, and category -->
      <form method="GET" action="recommendations.php">
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
      include 'database.php'; //Connect to the database
      session_start(); //start a session
      $user_id=$_SESSION['UserId'];
      $category = $_GET['category']; //Get user's selected category 
      $sortby = $_GET['order_by']; //Get user's selected sort by option
      if ($category){ 
        //Query to get reommended item that matches user's selected category 
        $categorysql = "SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
        FROM Item i
        LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
        FROM `BidRecord` 
        GROUP BY ItemId) b on i.ItemId = b.ItemId
        INNER JOIN (SELECT ItemId, ImageLocation
        FROM Image
        WHERE ImageId IN (SELECT min(ImageId) 
                         FROM Image
                         GROUP BY ItemId)
        and ItemId In (SELECT rs.ItemId
                       FROM (SELECT rs.ItemId,SUM(rs.Rank) AS RankSum
        FROM (SELECT b.UserId,b.ItemId, r.Rank
        FROM BidRecord b 
        INNER JOIN (SELECT UserId,COUNT( DISTINCT ItemId) as Rank 
        FROM `BidRecord` 
        WHERE ItemId In (SELECT ItemId 
                         FROM `BidRecord` 
                         WHERE UserId = $user_id) AND UserId != $user_id 
        GROUP BY UserId) r on r.UserId = b.UserId
        WHERE ItemId NOT IN (SELECT ItemId 
                         FROM `BidRecord` 
                         WHERE UserId = $user_id)
        GROUP BY ItemId, UserId) rs 
        GROUP BY ItemId
        ORDER BY RankSum DESC)rs)) f on i.ItemId = f.ItemId
        WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
        AND UserId != $user_id
        AND Category = '$category'
        Limit 0,3";
        $categoryresult = $conn->query($categorysql);
        if ($categoryresult->num_rows > 0){
          while($row = $categoryresult->fetch_assoc() ){
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $description = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_listing_li function defined in utilities.php
            print_listing_li($item_id, $title, $description, $price, $num_bids, $end_time, $image, $time_remaining);
          }
        }else{
          echo ('<div class="d-flex justify-content-center"><h3>NOTHING MATCHES YOUR SEARCH</h3></div>
          <br><div class="d-flex justify-content-center"><p>Please check the spelling or try less specific search terms.</p></div>');
        }
      }else if ($sortby){ //If user select an option from the sort by list
        if($sortby=='pricehigh'){ //sort item by price from highest to lowest
          ////Query to get recommended item and order by price from highest to lowest
          $maxsortsql = "SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation
          FROM Image
          WHERE ImageId IN (SELECT min(ImageId) 
                           FROM Image
                           GROUP BY ItemId)
          and ItemId In (SELECT rs.ItemId
                         FROM (SELECT rs.ItemId,SUM(rs.Rank) AS RankSum
          FROM (SELECT b.UserId,b.ItemId, r.Rank
          FROM BidRecord b 
          INNER JOIN (SELECT UserId,COUNT( DISTINCT ItemId) as Rank 
          FROM `BidRecord` 
          WHERE ItemId In (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id) AND UserId != $user_id 
          GROUP BY UserId) r on r.UserId = b.UserId
          WHERE ItemId NOT IN (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id)
          GROUP BY ItemId, UserId) rs 
          GROUP BY ItemId
          ORDER BY RankSum DESC)rs)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
          AND UserId != $user_id
          ORDER BY Price DESC
          Limit 0,3";
          $maxsortresult = $conn->query($maxsortsql);
          while($row = $maxsortresult->fetch_assoc()) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $description = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_listing_li function defined in utilities.php
            print_listing_li($item_id, $title, $description, $price, $num_bids, $end_time, $image, $time_remaining);
          };
        }else if($sortby=='pricelow'){ //Sort price from lowest to highest 
          //Query to get recommended item and order by price from lowest to highest
          $minsortsql = "SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation
          FROM Image
          WHERE ImageId IN (SELECT min(ImageId) 
                           FROM Image
                           GROUP BY ItemId)
          and ItemId In (SELECT rs.ItemId
                         FROM (SELECT rs.ItemId,SUM(rs.Rank) AS RankSum
          FROM (SELECT b.UserId,b.ItemId, r.Rank
          FROM BidRecord b 
          INNER JOIN (SELECT UserId,COUNT( DISTINCT ItemId) as Rank 
          FROM `BidRecord` 
          WHERE ItemId In (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id) AND UserId != $user_id 
          GROUP BY UserId) r on r.UserId = b.UserId
          WHERE ItemId NOT IN (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id)
          GROUP BY ItemId, UserId) rs 
          GROUP BY ItemId
          ORDER BY RankSum DESC)rs)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
          AND UserId != $user_id
          ORDER BY Price ASC
          Limit 0,3";
          $minsortresult = $conn->query($minsortsql);
          while($row = $minsortresult->fetch_assoc()) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $description = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_listing_li function defined in utilities.php
            print_listing_li($item_id, $title, $description, $price, $num_bids, $end_time, $image, $time_remaining);
          };
        }else if ($sortby=="date"){ //sort by date
          //Query to get recommended item and order by expirty date from soonest to latest
          $datesortsql = "SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
          FROM Item i
          LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
          FROM `BidRecord` 
          GROUP BY ItemId) b on i.ItemId = b.ItemId
          INNER JOIN (SELECT ItemId, ImageLocation
          FROM Image
          WHERE ImageId IN (SELECT min(ImageId) 
                           FROM Image
                           GROUP BY ItemId)
          and ItemId In (SELECT rs.ItemId
                         FROM (SELECT rs.ItemId,SUM(rs.Rank) AS RankSum
          FROM (SELECT b.UserId,b.ItemId, r.Rank
          FROM BidRecord b 
          INNER JOIN (SELECT UserId,COUNT( DISTINCT ItemId) as Rank 
          FROM `BidRecord` 
          WHERE ItemId In (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id) AND UserId != $user_id 
          GROUP BY UserId) r on r.UserId = b.UserId
          WHERE ItemId NOT IN (SELECT ItemId 
                           FROM `BidRecord` 
                           WHERE UserId = $user_id)
          GROUP BY ItemId, UserId) rs 
          GROUP BY ItemId
          ORDER BY RankSum DESC)rs)) f on i.ItemId = f.ItemId
          WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
          AND UserId != $user_id
          ORDER BY AuctionEndDateTime ASC
          Limit 0,3";
          $dateresult = $conn->query($datesortsql);
          while($row = $dateresult->fetch_assoc()) {
            $item_id = $row['ItemId'];
            $title = $row['ItemName'];
            $description = $row['Description'];
            $price = $row['Price'];
            $num_bids = $row['BidsCount'];
            $end_time = new DateTime($row['AuctionEndDateTime']);
            $image = $row['ImageLocation'];
            // Print out item details using the print_listing_li function defined in utilities.php
            print_listing_li($item_id, $title, $description, $price, $num_bids, $end_time, $image, $time_remaining);
          };
        }
      }else{
        $sql = "SELECT i.ItemId, ItemName,Description,Cond_,Category,Greatest(StartingBid,COALESCE(b.HighestBid,0)) as Price,AuctionEndDateTime, COALESCE(b.BidsCount,0) as BidsCount,f.ImageLocation
        FROM Item i
        LEFT JOIN (SELECT ItemId, MAX(BidAmount) AS HighestBid, COUNT(BidAmount) AS BidsCount
        FROM `BidRecord` 
        GROUP BY ItemId) b on i.ItemId = b.ItemId
        INNER JOIN (SELECT ItemId, ImageLocation
        FROM Image
        WHERE ImageId IN (SELECT min(ImageId) 
                         FROM Image
                         GROUP BY ItemId)
        and ItemId In (SELECT rs.ItemId
                       FROM (SELECT rs.ItemId,SUM(rs.Rank) AS RankSum
        FROM (SELECT b.UserId,b.ItemId, r.Rank
        FROM BidRecord b 
        INNER JOIN (SELECT UserId,COUNT( DISTINCT ItemId) as Rank 
        FROM `BidRecord` 
        WHERE ItemId In (SELECT ItemId 
                         FROM `BidRecord` 
                         WHERE UserId = $user_id) AND UserId != $user_id 
        GROUP BY UserId) r on r.UserId = b.UserId
        WHERE ItemId NOT IN (SELECT ItemId 
                         FROM `BidRecord` 
                         WHERE UserId = $user_id)
        GROUP BY ItemId, UserId) rs 
        GROUP BY ItemId
        ORDER BY RankSum DESC)rs)) f on i.ItemId = f.ItemId
        WHERE AuctionEndDateTime >= CURRENT_TIMESTAMP
        AND UserId != $user_id
        Limit 0,3";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
          $item_id = $row['ItemId'];
          $title = $row['ItemName'];
          $description = $row['Description'];
          $price = $row['Price'];
          $num_bids = $row['BidsCount'];
          $end_time = new DateTime($row['AuctionEndDateTime']);
          $image = $row['ImageLocation'];
          // Print out item details using the print_listing_li function defined in utilities.php
          print_listing_li($item_id, $title, $description, $price, $num_bids, $end_time, $image, $time_remaining);
        };
      }
      $conn->close(); //Close connection
    ?> 
    </ul>
  <!-- If user is not logged in, message below will be shown to indicate they need to log in first before access any contents -->
  <?php else :?>
    <p>Please Log in to see the contents :)</p>
  <?php endif?>
</div>
<?php include_once("footer.php")?>
