<?php 
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include "../common/header.php";
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /WBS/admin/login.php");
    exit;
}
require_once"../common/db_connection.php";

$meter_number = $bill_number =$complaint_number = $consumer_number = $admin_id = $sql = "";
$search_by = $col_names = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

	$search_type = $_POST["search_type"];
	$search_by = trim($_POST["search"]);

	switch ($search_type) {
		case '1':
				$sql = "SELECT * FROM consumers WHERE consumer_num = :search_by ";
				$col_names = array("Consumer Number", "Email", "Address", "Name", "Meter Number");		
			break;
		case '2':
				$sql = "SELECT * FROM bills WHERE bill_num = :search_by ";
				$col_names = array("Amount Payable", "Due Date", "Payment Status", "Meter Number", "Bill Number" );
			break;
		case '3':
				$sql = " SELECT * FROM complaints WHERE complaint_num = :search_by ";
				$col_names = array("Complaint Date", "Complaint", "Consumer Number", "Complaint Number");
			break;
		case '4':
			 	$sql = " SELECT * FROM meters WHERE meter_num = :search_by ";
			 	$col_names = array("Meter Number", "Reading Date", "Reading-1", "Reading-2", "Reading-3", "Reading-4", "Reading-5", "Rate of Charge" );
			break;
		case '5':
				$sql = " SELECT name, username, email, admin_id FROM admins WHERE admin_id = :search_by ";
				$col_names = array("Name", "Username", "Email", "Admin Id");
	}

	if($stmt = $pdo->prepare($sql)){
		$stmt->bindParam(":search_by", $search_by, PDO::PARAM_STR);			

			if ($stmt->execute()) {
				if($stmt->rowCount() > 0){
					$row = $stmt->fetch();
					echo "<br><table id = 'review'><th colspan='2'>Details</th>";
					for($i = 0; $i < $stmt->columnCount(); $i++){
						echo "<tr><td>".$col_names[$i]."</td>";
						echo "<td>".$row[$i]."</td></tr>"; 
					}
					echo "</table>";
				} else {
					echo "<span id='error'>No data matches your search term.</span>";
				}
			}
	unset($stmt);
	}
unset($pdo);
}

?>

<h2>Review Module</h2>
Seacrh by::
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
	<select name="search_type">
				<option value="1">Consumer Number</option>
				<option value="2">Bill Number</option>
				<option value="3">Complaint Number</option>
				<option value="4">Meter Number</option>
				<option value="5">---Admin ID---</option>
				</select> 
			
			<input type="text" name="search" required="true" placeholder="Input here.." autofocus="true"> <br/>

		<input type="submit">
</form>
<?php
include "../common/footer.php";
?>