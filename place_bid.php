<!-- Email Address  
    //System email that used to auto email to seller/buyer
        Email Address: ucldbgroup3@gmail.com
        Password: database1234
    
    //Email for testing
        Email Address:0178group3@gmail.com
        Password: groupH
-->

<?php
    // Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php'; ////Load Composer's autoloader
    include 'database.php'; //Connect to database
    $i=0; // Variable used to store highest bidamount
    // When buyer submit a bid using the place bid form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Get current session user ID and item ID
        session_start();
        $user_id = $_SESSION['UserId'];
        $item_id = $_SESSION['ItemId'];
        $bidAmount = $_POST['bid'];

        // Query to get User ID with the maximum bid of current session item 
        // and set it as the winner of the auction
        $j=0; 
        $currentmaxbiddersql = mysqli_query($conn,"SELECT UserId, BidAmount
        FROM BidRecord
        WHERE ItemId=$item_id 
        ORDER BY BidDateTime DESC");
        while($row = $currentmaxbiddersql->fetch_assoc()) {
            if($row['BidAmount']>$j){
                $j=$row['BidAmount'];
                $Highest = $row['UserId'];
            }
            $_SESSION['maxbidder'] = $Highest;  
        };
        $maxbidderid=$_SESSION['maxbidder'];  //Store User Id with maximum bid of current session item in variable $maxbidderid

        //Get maxbidder user (i.e., winner )details (i.e.,firstname, email) using its id from method above
        $maxbiddersql =mysqli_query($conn,"SELECT * FROM User WHERE UserId ='$maxbidderid'");
        while($row = $maxbiddersql->fetch_assoc()) {
            $maxbidfirstname = $row['FirstName'];
            $maxbidemail = $row['Email'];
        };

        //Query to get current max bid amount of the item from BidRecord
        $checkbidsql = mysqli_query($conn,"SELECT BidAmount 
        From BidRecord 
        WHERE ItemId='$item_id'");
        while($row = $checkbidsql->fetch_assoc()) {
            $current_bidAmount=$row['BidAmount'];
            if($i>$current_bidAmount){
                $current_bidAmount=$i;
            }
            $i = $row['BidAmount'];
        };

        //Query to get startingbid of the item. 
        //Used to ensure that first bidder start the bid with at least the starting bid set by the seller
        $nobidsql = mysqli_query($conn,"SELECT StartingBid From Item WHERE ItemId='$item_id'");
        while($row = $nobidsql->fetch_assoc()) {
            $startingPrice=$row['StartingBid'];
            if($i>$startingPrice){
                $startingPrice=$i;
            }
            $i = $row['StartingBid'];
        };

        // If-else statement to deal with submission of place bid form
        if($_SESSION['anybid']=="NoBid"){ // If there's no previous bid of current item
            if ($bidAmount>=$startingPrice){ // Check if the current user submitted bid amount is hgiher than seller's starting price
                //Insert new bid into bidrecord 
                $placebidsql = "INSERT INTO `BidRecord` (`BidRecordId`, `ItemId`, `UserId`, `BidAmount`, `BidDateTime`) VALUES (NULL, '$item_id', '$user_id', '$bidAmount', CURRENT_TIMESTAMP)";
                $run = mysqli_query($conn, $placebidsql) or die(mysqli_error($conn));
                // Alert Message to notify user has sucessfully uploaded the first bid of the item!
                echo '<script language="JavaScript" type="text/javascript"> 
                if (window.confirm("Success!"))
                {
                    history.go(-1); 
                }
                </script>';
            }else{ 
                echo '<script language="JavaScript" type="text/javascript">
                if (window.confirm("Sorry please place a bid highr than the starting price!"))
                {
                    history.go(-1); 
                }
                </script>';
            }
        }else if ($_SESSION['anybid']=="YesBid"){ // If there's previous bid record of current item
            if ($bidAmount>$current_bidAmount){ // Check if the current user submitted bid amount is hgiher than item maxbidamount 
                //insert new bidrecord into database 
                $placebidsql = "INSERT INTO `BidRecord` (`BidRecordId`, `ItemId`, `UserId`, `BidAmount`, `BidDateTime`) VALUES (NULL, '$item_id', '$user_id', '$bidAmount', CURRENT_TIMESTAMP)";
                $run = mysqli_query($conn, $placebidsql) or die(mysqli_error($conn));
                //Send auto email to notify maxbidder  notify that they are outbid; no longer the max bidder
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPSecure = 'tls'; // Secure transfer enabled REQUIRED for GMail
                $mail->SMTPAutoTLS = false;
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->Port = 587;
                $mail->Username = 'ucldbgroup3'; 
                $mail->Password = 'database1234';                                  
                $mail->setFrom('ucldbgroup3@gmail.com', 'Group3Auction Team'); // Sender email and name
                $mail->addAddress($maxbidemail, $maxbidfirstname); // Recipent email and name
                //Email message
                $mail->Subject = 'ALERT - Outbid Notification';
                $mail->Body    = "Dear ".$maxbidfirstname."\r\n
                Someone else bidded the same item with higher bid than you, would you consider to place a higher bid? \r\n 
                Log back into your account to place a hgiher bid if you want :)\n
                Best Regards,\n Group3Auction Team";
                $mail->send();
                // Alert Message to notify user has sucessfully placed a bid of the item!
                echo '<script language="JavaScript" type="text/javascript">
                if (window.confirm("Success!"))
                {
                    history.go(-1); 
                }
                </script>';
            }else{
                echo '<script language="JavaScript" type="text/javascript">
                if (window.confirm("Sorry please place a higher bid than current bid!"))
                {
                    history.go(-1); 
                }
                </script>';
            }
        }else{
                echo '<script language="JavaScript" type="text/javascript">
                if (window.confirm("Error"))
                {
                    history.go(-1); 
                }
                </script>';
        }
    }
    $conn->close();
?>
