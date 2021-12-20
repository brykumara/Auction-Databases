<!-- CSS styling for image slideshow on listing.php -->
<style>
  body {
    font-family: Arial;
    margin: 0;
  }
  * {
    box-sizing: border-box;
  }
  img {
    vertical-align: middle;
  }
  .container {
    position: relative;
  }
  .mySlides {
    display: none;
  }
  .prev,
  .next {
    cursor: pointer;
    position: absolute;
    top: 40%;
    width: auto;
    padding: 16px;
    margin-top: -50px;
    color: white;
    font-weight: bold;
    font-size: 20px;
    border-radius: 0 3px 3px 0;
    user-select: none;
    -webkit-user-select: none;
  }
  .next {
    right: 0;
    border-radius: 3px 0 0 3px;
  }
  .prev:hover,
  .next:hover {
    background-color: rgba(0, 0, 0, 0.8);
  }
</style>

<?php 
  include_once("header.php");
  require("utilities.php");
  include 'database.php'; //Connect to the database
  $item_id = $_GET['item_id'];
  $user_id = $_SESSION['UserId'];

  //Query to get item data based on item_id
  $itemsql = "SELECT * FROM `Item` WHERE ItemId = $item_id";
  $itemresult = $conn->query($itemsql);
  while($row = $itemresult->fetch_assoc()) {
    $title = $row['ItemName'];
    $item_user = $row['UserId'];
    $description = $row['Description'];
    $condition = $row['Cond_'];
    $price = $row['StartingBid'];
    $category = $row['Category'];
    $num_bids = $row['BidsCount'];
    $end_time = new DateTime($row['AuctionEndDateTime']);
  };
  $now = new DateTime(); // Get current time 
  // Calculate time to auction end:
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }

  // Query to get image data from database based on item_id
  $imagesql = "SELECT ImageLocation FROM Image WHERE ItemId = $item_id";
  $res = $conn->query($imagesql);

  //Query to get current maximum bid amount of the item using item ID
  $checkbidsql = mysqli_query($conn,"SELECT BidAmount From BidRecord WHERE ItemId='$item_id'");
  while($row = $checkbidsql->fetch_assoc()) {
    $current_bidAmount=$row['BidAmount'];
    if($i>$current_bidAmount){
      $current_bidAmount=$i;
      }
      $i = $row['BidAmount'];
  };

  //Query to check if user already select item as his/her favourite
  $findexistingsql = "SELECT * FROM WatchList WHERE UserId = $user_id AND ItemId=$item_id";
  $findexistingresult = mysqli_query($conn,$findexistingsql);
  $count = mysqli_num_rows($findexistingresult);
  //If match with User Id and Item Id, then table row must be greater than 0 
  if ($count>0) {
    $_SESSION['favouritestatus']="alreadyin"; //set session variable as already in watchlist
  }else{
    $_SESSION['favouritestatus']="notin"; 
  }

  $conn->close();
?>

<div class="container">
  <!-- Print Name of the item as the title of the page-->
  <br><h1 class="my-3"><?php echo($title); ?></h1><hr><br> 
  <div class="row"> <!--Start of first row -->
    <!-- Left Column: Item details -->
    <div class="col-sm-8"> 
      <!-- Print item details -->
      <div class="itemDescription">
        <?php 
          echo('<strong>Description</strong>: '.$description.'<br>'); 
          echo('<strong>Cateogry</strong>: '.$category.'<br>');
          echo('<strong>Condition</strong>: '.$condition.'<br>');
        ?>
      </div>
    </div>
    <!-- Right Column: Favourite button for watchlist  -->
    <div class="col-sm-4 align-self-center">
      <!-- Check if the auction item hasn't ended -->
      <?php if ($now < $end_time): session_start(); ?> 
        <br>
        <!-- Favourite button for watchlist -->
        <form method="POST" action="watchlist_funcs.php">
          <?php 
            //If session favourite status is already in (set earlier on this page)
            //then display "Remove from Watchlist" button in red colour to indicate it's already in user's watchlist
            if($_SESSION['favouritestatus']=="alreadyin"): 
              $_SESSION['ItemId']=$item_id; 
              $_SESSION['functionname'] ="remove_to_watchlist"; //Set session function name for watchlist_funcs.php
            ?>
              <button type="submit" class="btn btn-danger btn-sm">Remove from WatchList <?php echo"<img id=".$item_id." src='favon.jpeg' width=30px>"?></button>
            <?php endif?>
            <!-- Else-if session favourite status is not in (set earlier on this page) -->
            <!-- then display "Add to Watchlist" button in grey colour to indicate it's not on user's watchlist -->
            <?php if ($_SESSION['favouritestatus']=="notin"):
              $_SESSION['ItemId']=$item_id; 
              $_SESSION['functionname'] ="add_to_watchlist"; //Set session function name watchlist_funcs.php
          ?>
            <button type="submit"class="btn btn-outline-secondary btn-sm" >Add to Watchlist <?php echo"<img id=".$item_id." src='favoff.jpeg' width=30px>"?></button>
          <?php endif?>
        </form>
      <?php endif?>
    </div>
  </div>  <!--End of first row-->

  <div class="row">  <!--Start of second row  -->
  <!-- Left Column: Image -->
  <div class="col-sm-8"> 
    <br>
    <!-- If image found in the database -->
    <?php if($res->num_rows > 0){ ?> 
      <div class="container">
        <?php while($row = $res->fetch_assoc()){?>
          <!-- Display image as a slideshow -->
          <div class="mySlides">
            <?php echo "<img src='ItemPhotos/".$row['ImageLocation']."' width=100%>";?>
          </div>
        <?php } ?>
        <a class="prev" onclick="plusSlides(-1)">❮</a>
        <a class="next" onclick="plusSlides(1)">❯</a>
      </div>
      <?php }else{ ?>
        <p class="status error">Image(s) not found...</p> <!--Error message if no image found in the database -->
    <?php }?>
  </div>
  <!-- Right Column: Bidding Information -->
  <div class="col-sm-4"> 
    <!-- If auction has already ended -->
    <?php if ($now > $end_time): ?>
      <p style="color:Tomato;">This auction ended on <?php echo(date_format($end_time, 'd/m/y')) ?></p>
    <!-- If auction has not ended -->
    <?php else: ?>
      <!-- Display remaining time -->
      <p style="color:Green;">Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>
      <!-- Display starting price set by the seller -->
      <?php echo('<strong>Starting Price</strong>: <i>£'.$price.'</i><br>');?>
      <!-- Display Current max bid of the item -->
      <p><strong>Current bid: </strong> 
        <?php 
          // Check if there's any previous bid record of the item 
          if($current_bidAmount!=NULL){
            session_start();
            $_SESSION['anybid'] = "YesBid";
            echo ('£'.$current_bidAmount.'');
          }else{
            session_start();
            $_SESSION['anybid'] = "NoBid";
            echo "No Bid Yet!";
          }
        ?>
      </p> 
      <hr>
      <!-- Bidding form -->
      <?php 
        include 'database.php'; //Connect to the database
        session_start();
        $_SESSION['ItemId'] = $item_id;
      ?>
      <!-- Check if current user is the seller -->
      <?php if ($item_user!=$user_id):?>
          <!-- If it's not the seller, display a place bid form for buyer -->
          <form method="POST" action="place_bid.php">
          <p>Place Your Bid Here:</p>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">£</span>
            </div>
          <input type="number" class="form-control" name="bid">
          </div>
          <button type="submit" class="btn btn-primary form-control">Place bid</button>
        </form>
      <?php endif ?>
      <?php $conn->close(); //Close connection?>
      <br><br><hr>
      <!-- Display bid history  -->
      <div>
        <p>Bid History:</p>
        <!-- get bidrecord data and print in a table -->
        <table class="table table-striped" style='text-align:center'>
          <tr>
            <th>Bid Amount</th>
            <th>Bid Date & Time</th>
          </tr>
          <?php 
            include 'database.php'; //Connect to the database
            //Query to get data from BidRecord table depend on item_id order by the price from highest to lowest
            $bidtablesql = "SELECT BidAmount, BidDateTime FROM BidRecord WHERE ItemId =  $item_id ORDER BY BidAmount DESC";
            $bidrtableesult = $conn->query($bidtablesql); 
            while($row = $bidrtableesult->fetch_assoc()) { ?>
            <tr>
              <td><?php echo $row['BidAmount']?></td>
              <td><?php echo $row['BidDateTime']?></td>
            </tr>
          <?php }$conn->close();?>
        </table>
      </div>
    <?php endif ?>
  </div> 
</div> 
<?php include_once("footer.php")?>

<!-- Script to deal with slideshow  -->
<script> 
  var slideIndex = 1;
  showSlides(slideIndex);
  
  function plusSlides(n){
    showSlides(slideIndex += n);
  }

  function currentSlide(n) {
    showSlides(slideIndex = n);
  }

  function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex-1].style.display = "block";
  }
</script>

