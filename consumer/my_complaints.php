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

	$sql = "SELECT * FROM complaints WHERE consumer_num = :search_by ";
	$col_names = array("Complaint Date", "Complaint", "Consumer Number", "Complaint Number");

	if($stmt = $pdo->prepare($sql)){
		$stmt->bindParam(":search_by", $search_by, PDO::PARAM_STR);
		$search_by = $_SESSION["consumer_number"];

		if($stmt->execute()){
			if($stmt->rowCount() > 0){
				//$row = $stmt->fetch();
				$rowNumber = 0;
				while ($row = $stmt->fetch()) {
					echo "<br><table id = 'my_complaints'><th colspan='2'>My Complaint#".++$rowNumber."<th>";
					for($i = 0; $i < $stmt->columnCount(); $i++){
						echo "<tr><td>".$col_names[$i]."</td>";
						echo "<td>".$row[$i]."</td></tr>"; 
					}
					echo "</table>";
				} 
			}else {
				echo "You've not filed any complaints. Thank You!";
			}
		}
	unset($stmt);
	}
unset($pdo);

 include "../common/footer.php";
 ?>