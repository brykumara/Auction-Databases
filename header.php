<?php 
  session_start(); //session to keep user logged in until they click on 'Log out' button
  include 'database.php'; // Connect to the database
	error_reporting(E_ERROR | E_PARSE);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap and FontAwesome CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom CSS file -->
    <link rel="stylesheet" href="css/custom.css">
    <title>Group3Auction</title> <!--browswer tap name-->
  </head>

  <body>
    <!-- Navbars -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
      <!-- Navigate to index.php when user click on it -->
      <a class="navbar-brand" href="index.php">Group3Auction</a> 
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"> 
          <?php
            // Displays either login or logout on the right, depending on user's current status (session).
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
              echo '<a class="nav-link" href="logout.php">Logout</a>';
            }else {
              echo '<button type="button" class="btn nav-link" ><a class="nav-link" href="login.php">Login<a/></button>';
            }
          ?>
        </li>
      </ul>
    </nav>

    <!-- All buttons navigating to different pages.  -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="browse.php">Browse</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="watchlist.php">WatchList</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="recommendations.php">Recommendation</a>
          </li>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="mylistings.php">My Listing</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <!-- Buyer and seller buttons -->
          <li><a class="btn btn-success my-2 my-sm-0" href="mybids.php" role="button">My Bid</a></li>
          <li><a class="btn btn-danger my-2 my-sm-0" href="create_auction.php" role="button">Sell</a></li>
        </form>
      </div>
    </nav>
  </body>
</html>
