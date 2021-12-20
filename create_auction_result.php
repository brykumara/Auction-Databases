<?php include_once("header.php");?>
<div class="container my-5">
    <?php
        session_start(); //Start a session
        $userId =  $_SESSION['UserId']; // Get session variables (i.e. User ID) that were set on login.php
        // When seller submit an auction form 
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if all required form's fields are NOT empty
            if(!empty($_POST['ItemName']) && 
            !empty($_POST['Description']) && !empty($_POST['Cond_']) && 
            !empty($_POST['Category']) && !empty($_POST['StartingBid']) && 
            !empty($_POST['AuctionEndDateTime'])) {
                // Receive all input values from "Create Auction Form"	 
                $title = $_POST['ItemName'];
                 // Removes any special characters from description that may interfere with the query operations
                $description = mysqli_real_escape_string($conn, $_POST['Description']);
                $condition = $_POST['Cond_'];
                $category = $_POST['Category'];
                $startingBid = $_POST['StartingBid'];
                $reservePrice = $_POST['ReservePrice'];
                $auctionEndDateTime = date ('Y-m-d H:i:s', strtotime($_POST['AuctionEndDateTime']));
                //If no value for 'ReservePrice' field, then set if as 0
                if (empty($_POST['ReservePrice'])){
                    $reservePrice = 0;
                }
                //Query to insert item into the database as a new auction 
                $sql = "INSERT INTO `Item`(`ItemId`, `UserId`, `ItemName`, `Description`, `Cond_`, `itemCreateDateTime`, `Category`, `StartingBid`, `ReservePrice`, `AuctionEndDateTime`)  
                VALUES (NULL,'$userId','$title','$description','$condition',CURRENT_TIMESTAMP,'$category','$startingBid','$reservePrice','$auctionEndDateTime')";            
                $run = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                if($run) {
                    // If all is successful, let user know.
                    session_start();
                    //Query to get Item ID that seller just created
                    $searchsql = "SELECT * FROM Item WHERE UserId = '$userId' AND ItemName='$title'";
                    $searchresult = $conn->query($searchsql);
                    while($row = $searchresult->fetch_assoc() ){
                        $_SESSION['ItemId']= $row['ItemId'];
                    }
                    // If all is successful, let user know and navigate user to page for image upload
                    echo ('<div class="text-center">Details successfully recorded! <a href="upload_image.php"> Please upload your images.</a></div>');
                } else {
                    echo "Error: " . $sql . "
                    " . mysqli_error($conn);
                }
            }else{
                echo '<script language="JavaScript" type="text/javascript">
                if (window.confirm("Please fill all the necessary fields"))
                {
                    history.go(-1); 
                }
                </script>';
            }
            mysqli_close($conn);
        }
    ?>
</div>
<?php include_once("footer.php")?>

