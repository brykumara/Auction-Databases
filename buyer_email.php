<!-- Email Address  
    //System email that used to auto email to seller/buyer
        Email Address: ucldbgroup3@gmail.com
        Password: database1234
    
    //Email for testing
        Email Address:0178group3@gmail.com
        Password: groupH
-->

<!-- This page runs in the background to check 
if any active acution's times' up 
and send auto email to seller and buyer to notify them the winner. -->
<?php
    // Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php'; ////Load Composer's autoloader
    include 'database.php'; //Connect to database
    
    //Query to check item that's times up
    $currentmaxbidsql = mysqli_query($conn,"SELECT b.ItemId,MAX(b.BidAmount)
    FROM BidRecord b
    JOIN (SELECT ItemId, AuctionEndDateTime
	FROM `Item`) i on b.ItemId = i.ItemId
	WHERE AuctionEndDateTime=CURRENT_TIMESTAMP 
	GROUP BY ItemId");
    $count = mysqli_num_rows($currentmaxbidsql);
    if(mysqli_num_rows($currentmaxbidsql)>0){
        // Get the Item ID and its current max bid amount
        while($row = $currentmaxbidsql->fetch_assoc()) {
            $itemId = $row['ItemId'];
            $MaxBid=$row['MAX(b.BidAmount)'];
        }

        //Query to get the max bidder user ID and item id baded on the max bid
        $maxbiddersql = "SELECT * FROM BidRecord WHERE BidAmount=$MaxBid";
        $maxbidderresult = $conn->query($maxbiddersql);
        while($row = $maxbidderresult->fetch_assoc()) {
            $_SESSION['maxbidder'] = $row['UserId'];
            $_SESSION['End_item'] = $row['ItemId'];
        };
        $winnerbidder = $_SESSION['maxbidder']; // Set the max bidder as winner

        // Query to get Item name based on item ID
        $winneritem = $_SESSION['End_item'];
        $winneritemsql = "SELECT * FROM Item WHERE ItemId = '$winneritem'";
        $winneritemresult = $conn->query($winneritemsql);
        while($row = $winneritemresult->fetch_assoc()) {
            $_SESSION['ItemName'] = $row['ItemName'];
        }
        $winneritem = $_SESSION['ItemName']; //Set item as winner item

        //Find winner details (i.e., ID) and send them email to notify he/she is the winner of the acution
        $winnersql = "SELECT * FROM User WHERE UserId = '$winnerbidder'";
        $winnerresult = $conn->query($winnersql);
        while($row = $winnerresult->fetch_assoc()) {
            $winnerfirstname = $row['FirstName'];
            $winneremail = $row['Email'];
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
            $mail->SMTPAutoTLS = false;
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = 'ucldbgroup3';
            $mail->Password = 'database1234';                                  
            $mail->setFrom('ucldbgroup3@gmail.com', 'Group3Auction Team'); // sender email address + name
            $mail->addAddress($winneremail, $winnerfirstname); //Recipients email address + name
            // Email message to winner (i.e., buyer) to congradulate he/she becoming the winner of the auction
            $mail->Subject = 'Congratulations!';
            $mail->Body    = "Dear ".$winnerfirstname."
            \nCongratulations! You are the winner of the auction item - ".$winneritem."!:)\r\n Here are your details: \n Your winning Item:".$winneritem."\n Your winning bid: £".$MaxBid."\r\nSeller will soon contact you about the payment!
            \nBest Regards,
            \n Group3Auction Team";
            $mail->send();
        };

        // Query to get the seller ID based on item ID
        $sellersql = "SELECT UserId FROM Item WHERE ItemId = $itemId";
        $sellerresult = $conn->query($sellersql);
        while($row = $sellerresult->fetch_assoc()) {
            $seller = $row['UserId'];
        };

        // Query to get seller details (i.e., firstname and email) 
        //and send seller a email to notify them the winner 
        $sellerdetailssql = "SELECT * FROM User WHERE UserId = $seller";
        $sellerdetailsresult = $conn->query($sellerdetailssql);
        while($row=$sellerdetailsresult->fetch_assoc()){
            $sellerfirstname = $row['FirstName'];
            $selleremail = $row['Email'];
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
            $mail->SMTPAutoTLS = false;
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = 'ucldbgroup3';
            $mail->Password = 'database1234';                                  
            $mail->setFrom('ucldbgroup3@gmail.com', 'Group3Auction Team'); //sender email + name
            $mail->addAddress($selleremail, $sellerfirstname); //Recipients (i.e., seller email + name)
            // Email message to seller regarding the auction result
            $mail->Subject = 'Auction Result';
            $mail->Body    = "Dear ".$sellerfirstname."
            \n Your auction item - ".$winneritem." has now ended:)\r\n Here are the results: \n Item:".$winneritem."\n Winning bid: £".$MaxBid."\r\n Please contact the buyer regarding payment! Winner email:".$winneremail."
            \nBest Regards,
            \n Group3Auction Team";
            $mail->send();
        }
    }
?>

