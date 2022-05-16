<?php 
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include("../common/header.php"); 
	
	// Initialize the session

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["consumer_loggedin"]) || $_SESSION["consumer_loggedin"] !== true){
    header("location: /WBS/consumer/login.php");
    exit;
}

 require_once "../common/db_connection.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$sql = "INSERT INTO complaints (complaint_date, consumer_num, complaint)
		VALUES (NOW(), :consumer_number, :complaint) ";

	if($stmt = $pdo->prepare($sql)){
		$stmt->bindParam(":consumer_number", $param_consumer_num, PDO::PARAM_STR);
		$stmt->bindParam(":complaint", $param_complaint, PDO::PARAM_STR);

		$param_consumer_num = $_SESSION["consumer_number"];
		$param_complaint = trim($_POST["complaint"]);

		if($stmt->execute()){
			echo "You complaint has been registered successfully!";
		} else {
			echo "Oophs! Try later..";
		}
	unset($stmt);
	}
unset($pdo);	
}


?>

<h2>Register Complaint Module</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

				 <textarea name="complaint" rows="5" cols="50" maxlength="500" wrap="hard" required="true" placeholder="What is your complaint/feedback?..."></textarea>
				<br/>

				<input type="submit" value="Send">
	</form>
	<br/>
	<a href="my_complaints.php">
		<button id='pre_comp_button'>View previous complaints</button>
	</a>
<?php include("../common/footer.php"); ?>