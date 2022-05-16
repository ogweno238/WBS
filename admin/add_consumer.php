<?php 
session_start();
include("../common/header.php"); 
	// Initialize the session

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /WBS/admin/login.php");
    exit;
}

// Include config file
require_once "../common/db_connection.php";
  
//Initialize variables
$consumer_name = $consumer_number = $meter_number = $consumer_email = "";
$consumer_address = "";
$consumer_number_err = $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//IMPORTANT VALIDATION - check meter_number and consumer_number for duplicacy.
	$sql = "SELECT consumer_num, meter_num FROM consumers WHERE consumer_num = :consumer_number OR meter_num = :meter_number";

	 if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":consumer_number", $param_cnum, PDO::PARAM_STR);
            $stmt->bindParam(":meter_number", $param_meter_num, PDO::PARAM_STR);
            // Set parameters
            $param_cnum = trim($_POST["consumer_number"]);
            $param_meter_num = trim($_POST["meter_number"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $consumer_number_err = "This Consumer Number or Meter number is already registered!";
                    echo "<span id='error'>".$consumer_number_err."</span>";
                } else{
                    $consumer_number = trim($_POST["consumer_number"]);
                    $meter_number = trim($_POST["meter_number"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }

    // to do: 	input validation (optinal--can be done client side but 
	//			should be done server side for consistency)


    // Check input errors before inserting in database
    //  if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
       $sql = "INSERT INTO consumers (consumer_num, name, meter_num, email, address) VALUES (:consumer_num, :c_name, :meter_num, :c_email, :c_address)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":consumer_num", $param_cnum, PDO::PARAM_STR);
            $stmt->bindParam(":c_name", $param_cname, PDO::PARAM_STR);
            $stmt->bindParam(":meter_num", $param_meter_num, PDO::PARAM_STR);
            $stmt->bindParam(":c_email", $param_c_email, PDO::PARAM_STR);
            $stmt->bindParam(":c_address", $param_address, PDO::PARAM_STR);
            
            // Set parameters
            // $param_username = $username;
            // $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            $param_cnum = $consumer_number;
            $param_cname = trim($_POST["consumer_name"]);
            $param_meter_num = $meter_number;
            $param_c_email = trim($_POST["consumer_email"]);
            $param_address = trim($_POST["address"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                $success = "Consumer details have been successfully added.".
                     "<br><a href='meter_update.php'>Update Meter </a>.";
                echo "<span id='success'>".$success."</span>";
               // header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    //}

    // Close connection
    unset($pdo);

}
?>
	
You're logged in as  
 <?php echo htmlspecialchars($_SESSION["username"]); ?>

		<h2>Adding Consumers Module</h2>

		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> ">
		
				<input type="text" name="consumer_number" required="true"
                 placeholder="Consumer Number" autofocus="true"> <br/>

				<input type="text" name="consumer_name" required="true"
                 placeholder="Consumer Name"><br/>

                <input type="number" name="meter_number" min="0" max="100000"
                 required="true" placeholder="Meter Number"><br/>

                 <input type="email" name="consumer_email" required="true"
                  placeholder="Email"><br/>

                  <input type="text" name="address" placeholder="Address"><br/>

                  <input type="submit" name="add">
		</form>
	
<?php include ("../common/footer.php"); ?>
