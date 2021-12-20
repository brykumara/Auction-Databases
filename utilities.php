
<?php
  // Function used to display time remaining for the auction :
  // Helper function to help figure out what time to display
  function display_time_remaining($interval) {
    if ($interval->days == 0 && $interval->h == 0) { // Less than one hour remaining: print mins + seconds:
      $time_remaining = $interval->format('%im %Ss');
    }else if ($interval->days == 0) {// Less than one day remaining: print hrs + mins:
      $time_remaining = $interval->format('%hh %im');
    }else {// At least one day remaining: print days + hrs:
      $time_remaining = $interval->format('%ad %hh');
    }
    return $time_remaining;
  }

  // Function print_listing_li: prints an HTML <li> element containing an auction listing
  // For printing item as a list on browse.php
  function print_listing_li($item_id, $title, $desc, $price, $num_bids, $end_time, $image, $time_remaining){
    // Truncate long descriptions
    if (strlen($desc) > 250) {
      $desc_shortened = substr($desc, 0, 250) . '...';
    }else {
      $desc_shortened = $desc;
    }
    
    // Fix language of bid vs. bids
    if ($num_bids == 1) {
      $bid = ' bid';
    }else {
      $bid = ' bids';
    }
    
    // Calculate time to auction end
    $now = new DateTime(); //Get current timestamp
    if ($now < $end_time) {
      // Get interval:
      $time_to_end = date_diff($now, $end_time);
      $time_remaining = display_time_remaining($time_to_end) . ' remaining'; //Calculate time remaining if the auction hasn't ended
    }else {
      $time_remaining = 'This auction has ended'; 

    }

    //Add root to image
    $image = '/ItemPhotos/' . $image; //Image path

    // Print HTML - item details included in the list
    echo('
      <li class="list-group-item d-flex justify-content-between">
        <img src='. $image .' width="125" height="150">
        <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
        <div class="text-left text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . '<div style="color:green">'.$time_remaining.'</div>' . '</div>
      </li>'
    );  
  }

  //Function print_mylisting_li: print seller's auction item on mylistings.php - item that they created
  // For printing item as a list on mylistings.php
  function print_mylisting_li($item_id, $title, $desc, $price, $num_bids, $end_time, $image, $time_remaining){
    // Truncate long descriptions
    if (strlen($desc) > 250) {
      $desc_shortened = substr($desc, 0, 250) . '...';
    }else {
      $desc_shortened = $desc;
    }

    // Fix language of bid vs. bids
    if ($num_bids == 1) {
      $bid = ' bid';
    }else {
      $bid = ' bids';
    }
    
    //Add root to image
    $image = '/ItemPhotos/' . $image; //Image path
    
    // Calculate time to auction end
    $now = new DateTime();
    if ($now > $end_time) {
      $time_remaining = 'This auction has ended';
    }
    else {
      // Get interval:
      $time_to_end = date_diff($now, $end_time);
      $time_remaining = display_time_remaining($time_to_end) . ' remaining';
    }
    
    // Print HTML
    echo('
      <li class="list-group-item d-flex justify-content-between">
        <img src='. $image .' width="125" height="150">
        <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened .  '</div>
        <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>' . $num_bids . $bid . '<br/>' . '<div style="color:green">'.$time_remaining.'</div>' . '</div>
      </li>'
    );
  }

  //Function print_mylisting_li: print seller's auction item on mylistings.php - item that they created
  // For printing item as a list on mylistings.php
  function print_mybidding_li($item_id, $title, $desc, $price, $end_time, $image, $time_remaining){
    // Truncate long descriptions
    if (strlen($desc) > 250) {
      $desc_shortened = substr($desc, 0, 250) . '...';
    }else {
      $desc_shortened = $desc;
    }
    
    //add root to image
    $image = '/ItemPhotos/' . $image; //Image path
    
    // If-else statment to print different details depending on time-remaining of each item.
    $now = new DateTime(); 
    if ($now > $end_time) {  // Calculate time to auction end
      $time_remaining = 'This auction has ended';
      // Print current user's maximum bid (not the item maximum bid)Show time remaining of each item 
      // Print time remaining in red to indicate auction has ended
      echo('
        <li class="list-group-item d-flex justify-content-between">
          <img src='. $image .' width="125" height="150">
          <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened .  '</div>
          <div class="text-center text-nowrap"><span style="font-size: 1.5em"> Your Highest Bid: £' . number_format($price, 2) . '</span><br/>' . '<div style="color:red">'.$time_remaining.'</div>' . '</div>
          </li>'
      );
    }else {
      // Get interval:
      $time_to_end = date_diff($now, $end_time);
      $time_remaining = display_time_remaining($time_to_end) . ' remaining';
      // Print time remaining in green to indicate auction hasn't ended
      echo('<li class="list-group-item d-flex justify-content-between">
        <img src='. $image .' width="125" height="150">
        <div class="p-2 mr-5"><h5><a href="listing.php?item_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened .  '</div>
        <div class="text-center text-nowrap"><span style="font-size: 1.5em">Your Highest Bid: £' . number_format($price, 2) . '</span> <br/>' . '<div style="color:green">'.$time_remaining.'</div>' . '</div>
        </li>'
      );
    } 
  }
?>
