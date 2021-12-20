
<?php 
    include_once("header.php");
    include 'database.php'; //Connect to the database
    // When user upload the image
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        session_start();
        $itemid = $_SESSION['ItemId'];
        // File upload configuration
        $target_dir = "ItemPhotos/"; //Page to save the image
        $extensions_arr = array("jpg","jpeg","png","gif"); // Valid file extensions
        $name = array_filter($_FILES['file']['name']); 
        if (!empty($name)){
            foreach($_FILES['file']['name'] as $key=>$val){
                $fileName = basename($_FILES['file']['name'][$key]); 
                $target_file = $target_dir . $fileName; 
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if(in_array($imageFileType,$extensions_arr) ){
                    // Upload file 
                    if(move_uploaded_file($_FILES['file']['tmp_name'][$key],$target_file)){
                        //Query to insert image into the database, linked with item ID
                        $query = "INSERT INTO Image (ImageId, ItemId, ImageLocation) VALUES (NULL, '$itemid', '".$fileName."')";
                        mysqli_query($conn,$query);
                        if ($query){
                            //Upon successful image upload, notify user
                            echo "Files are uploaded successfully."; 
                            header("refresh:0.5;url= mylistings.php");
                        }else{
                            echo '<script language="JavaScript" type="text/javascript">
                            if (window.confirm("Image Upload Unsuccessfully, please try again!"))
                            {
                                history.go(-1); 
                            }
                            </script>';
                        }
                    }
                }
            }  
        }else{ 
            echo 'Please select a file to upload.'; 
        } 
    }
  $conn->close(); //Close connection
?>

<!-- Upload Image Form -->
<div class="container">
    <!-- Check to see if the user is logged in or not -->
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true):?>
        <h2 class="my-3">Uploade Your Image Here</h2>
        <p style='color:red;'><span class="error">* required field</span></p>
        <div class="card">
            <div class="card-body">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type='file' name='file[]' multiple required />
                    <button type="submit" name="upload" class="btn btn-primary form-control">Submit Auction</button>
                </form>
            </div>
        </div>
    <!-- If user is not logged in, message below will be shown to indicate they need to log in first before access any contents -->
    <?php else :?>
        <p>Please Log in to see the contents :)</p>
    <?php endif?>
</div>
<?php include_once("footer.php")?>