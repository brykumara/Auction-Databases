<?php include_once("header.php");?>

<div class="container">
<!-- Create auction form -->
  <h2 class="my-3">Create New Auction</h2>
  <!-- Check to see if the user is logged in or not -->
  <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true):?>
  <p style='color:red;'><span class="error">* required field</span></p>
  <div class="card">
    <div class="card-body">
      <!-- Create auction form -->
      <!-- Navigate to create_auction_result.php to deal with submission -->
      <form method="post" action="create_auction_result.php">
        <div class="form-group row">
          <label for="ItemName" class="col-sm-2 col-form-label text-right">Title <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="ItemName" name="ItemName">
            <small id="titleHelp" class="form-text text-muted"> Item that you're selling, which will display in listings</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="Description" class="col-sm-2 col-form-label text-right">Description <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
            <textarea class="form-control" id="Description" name="Description" rows="4" placeholder="Full details of the listing to help bidders decide if it's what they're looking for"></textarea>
          </div>
        </div>
        <div class="form-group row">
          <label for="Cond_" class="col-sm-2 col-form-label text-right">Condition <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
            <select class="form-control" id="Cond_" name="Cond_">
              <option selected>Choose...</option>
              <option value="Very Good">Very Good</option>
              <option value="Good">Good</option>
              <option value="Fair">Fair</option>
              <option value="Poor">Poor</option>
              <option value="Very Poor">Very Poor</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="Category" class="col-sm-2 col-form-label text-right">Category <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
            <select class="form-control" id="Category" name="Category">
              <option selected>Choose...</option>
              <option value="Bedroom">Bedroom</option>
              <option value="Living room">Living Room</option>
              <option value="Kitchen">Kitchen</option>
              <option value="Bathroom">Bathroom</option>
              <option value="Study">Study</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="StartingBid" class="col-sm-2 col-form-label text-right">Starting price <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
	        <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="StartingBid" name="StartingBid">
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label for="ReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="ReservePrice" name="ReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="AuctionEndDateTime" class="col-sm-2 col-form-label text-right">End Date & Time <span class="text-danger">*  </span></label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="AuctionEndDateTime" name="AuctionEndDateTime">
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control">Next</button>
      </form>
    </div>
  </div>
  <!-- If user is not logged in, message below will be shown to indicate they need to log in first before access any contents -->
  <?php else :?>
		<p>Please Log in to see the contents :)</p>
	<?php endif?>
</div>

</div>
<?php include_once("footer.php")?>