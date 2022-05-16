<?php include("../common/header.php"); 
	
	// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /WBS/admin/login.php");
    exit;
}

// Include config file
require_once "../common/db_connection.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if( empty(trim($_POST["username"])) ){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT username FROM admins WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";    
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["cpassword"]))){
        $confirm_password_err = "Please confirm password.";   
    } else{
        $confirm_password = trim($_POST["cpassword"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO admins (username, password, name, email)
                 VALUES (:username, :password, :name, :email)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_password = $password;
            //$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_name = trim($_POST["name"]);
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                echo "<span id='success'>New admin added!</span>";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    } else {
        echo "<span id='error'>".$username_err." ".$password_err." ".$confirm_password_err."</span>";
    }
    
    // Close connection
    unset($pdo);
}
?>
<h2>Add New Admin</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		
         <input type="text" name="name" placeholder="New Admin Name" autofocus="true"><br/>

          <input type="text" name="username" placeholder="New Admin User Name"><br/>

          <input type="email" name="email" placeholder="Email"><br/>

          <input type="password" name="password" placeholder="Password"><br/>

          <input type="password" name="cpassword" placeholder="Confirm Password"><br/>

          <input type="submit" name="add"></td>
</form>

<?php include("../common/footer.php"); ?>