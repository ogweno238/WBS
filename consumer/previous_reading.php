<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["consumer_loggedin"]) || $_SESSION["consumer_loggedin"] !== true){
    header("location: /WBS/consumer/login.php");
    exit;
}

include("../common/header.php");

require_once"../common/db_connection.php";
 $sql = "SELECT reading1, reading2, reading3, reading4, reading5,
 		reading_date, rate
 		FROM meters
 		INNER JOIN consumers
 		ON consumers.meter_num = meters.meter_num
 		WHERE consumer_num = :consumer_number ";

 	if($stmt1 = $pdo->prepare($sql)){
 		$stmt1->bindParam(":consumer_number", $param_consumer_number, PDO::PARAM_STR);

 		$param_consumer_number = $_SESSION["consumer_number"];

 		if($stmt1->execute()){
 			if($stmt1->rowCount() > 0){
 			$row = $stmt1->fetch();

 			$readings = array($row["reading1"], $row["reading2"], $row["reading3"], $row["reading4"], $row["reading5"]);
 			rsort($readings);

 			$current_month = date("F-Y", strtotime($row["reading_date"]));
            $one_month_before = date("F-Y", strtotime("-1 month", strtotime($row["reading_date"])));
 			$two_month_before = date("F-Y", strtotime("-2 month", strtotime($row["reading_date"])));
 			$three_month_before = date("F-Y", strtotime("-3 month", strtotime($row["reading_date"])));
 			$four_month_before = date("F-Y", strtotime("-4 month", strtotime($row["reading_date"])));

			 $months = array($current_month, $one_month_before, $two_month_before, $three_month_before, $four_month_before);

 		echo "User: ". $_SESSION["cname"].
 			"<Consumer Number: ".$_SESSION["consumer_number"]."<br>";

 		echo"<table id = 'previous_reading'>
 				<th>Sl. no</th>
 				<th>For the Month of</th>
 				<th>Reading Recorded<br>(units)</th>
 				<th>Reading Date<br>(yyyy-dd-mm)</th>
 				<th>Rate of charge<br>(Rs./unit)</th>
 				<th>Amount<br>(Rs.)</th>";

 				for($i = 0; $i < 5; $i++){
 					$sl = $i+1; //serial number!! This makes me look stupid. Bwwaaagh!!
 					echo "<tr><td>".$sl."</td><td>".$months[$i]."</td> <td>".$readings[$i]."</td>";

 					 if($i == 0 ){
 						echo"<td>".$row["reading_date"]."</td>"; 	
 					}else{
 						echo "<td>Not Available</td>";
 					}

 					echo "<td>".$row["rate"]."</td><td>".$row["rate"]*$readings[$i]."</td></tr>";
 				}
 				echo "</table>";

 		 } else {
 		 	echo "Sorry we currently do not have your any past records!";
 		   }
 		}
 	unset($stmt1);
 	}
 unset($pdo);


 include "../common/footer.php";
 ?>
<!-- /////////////////////------PREVIOUSLY I WAS STUPID--------///////////////////////// -->
 <!--  
 	"I wrote the html below to print the table,
 	later it came to me that a repetiting table can be done through a loop
 	Dang!! I was so much dumb just an hour ago!!" 
			
			Okay, honestly I'm not deleting it because
			I wrote it with too much hard work and now
			I dont wanna lose it. NO.NOOOOO..."					
									-----Author

 				<tr>
 					<td>1</td>
 					<td>".$current_month."</td>
 					 <td>".$readings[4]."</td>
 					<td>".$row["reading_date"]."</td> 	
 					<td>".$row["rate"]."</td>
 					<td>".$row["rate"]*$readings[4]."</td>
 				</tr>
 				<tr>
 					<td>2</td>
 					<td>".$one_month_before."</td>
 					 <td>".$readings[3]."</td>
 					<td>Not Available</td> 	
 					<td>".$row["rate"]."</td>
 					<td>".$row["rate"]*$readings[3]."</td>
 				</tr> 			
 				<tr>
 					<td>3</td>
 					<td>".$two_month_before."</td>
 					 <td>".$readings[2]."</td>
 					<td>Not Available</td> 	
 					<td>".$row["rate"]."</td>
 					<td>".$row["rate"]*$readings[2]."</td>
 				</tr> 
 				<tr>
 					<td>4</td>
 					<td>".$three_month_before."</td>
 					 <td>".$readings[1]."</td>
 					<td>Not Available</td> 	
 					<td>".$row["rate"]."</td>
 					<td>".$row["rate"]*$readings[1]."</td>
 				</tr> 				
 				<tr>
 					<td>5</td>
 					<td>".$four_month_before."</td>
 					 <td>".$readings[0]."</td>
 					<td>Not Available</td> 	
 					<td>".$row["rate"]."</td>
 					<td>".$row["rate"]*$readings[0]."</td>
 				</tr>
 			</table> ";
 ////////////////////////////////////////////////////////////////////// -->