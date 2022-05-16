<?php 

// Initialize the session

session_start(); 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["consumer_loggedin"]) && $_SESSION["consumer_loggedin"] === true){
     header("location: /WBS/consumer/consumer_dashboard.php");
    exit;
}
 // Include config file
require_once "../common/db_connection.php";
 
// Define variables and initialize with empty values
$consumer_number =  $meter_number = "";
$consumer_number_err = $meter_number_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["consumer_number"]))){
        $consumer_number_err = "Please enter Consumer number";
    } else{
        $consumer_number = trim($_POST["consumer_number"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["meter_number"]))){
        $meter_number_err = "Please enter your meter number";
    } else{
        $meter_number = trim($_POST["meter_number"]);
    }
    // Validate credentials
    if(empty($consumer_number_err) && empty($meter_number_err)){
        // Prepare a select statement
         $query = "SELECT consumer_num, meter_num, name
                    FROM consumers 
                    WHERE consumer_num = :consumer_number";
        
        if($stmt = $pdo->prepare($query)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":consumer_number", $param_consumer_num, PDO::PARAM_STR);
            
            // Set parameters
            $param_consumer_num = trim($_POST["consumer_number"]);
        
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $consumer_number = $row["consumer_num"];
                        $cname = $row["name"];
                        $retrieved_meter_num = $row["meter_num"];

                        if($retrieved_meter_num == $meter_number){ 

                            // match found, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["consumer_loggedin"] = true;
                            $_SESSION["cname"] = $cname;
                            $_SESSION["consumer_number"] = $consumer_number;                            
                            // Redirect user to welcome (dashboard) page.
                            header("location: /WBS/consumer/consumer_dashboard.php");
                        } else{
                            // Display an error message if password is not valid
                            $meter_number_err = "The consumer/meter number you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $consumer_number_err = "No match found with that consumer number.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}

include("../common/header.php"); 
?>
	<legend>
		<h2>Consumer Login</h2>
         <span id="error"><?php echo $consumer_number_err."<br>".$meter_number_err; ?></span>

		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		
        <input type="text" name="consumer_number" placeholder="Consumer Number"><br/>

	   <input type="text" name="meter_number" placeholder="Meter Number"><br/>

       <input type="submit" name="Login" value="Login">
		</form>			

	</legend>

<?php include("../common/footer.php"); ?>