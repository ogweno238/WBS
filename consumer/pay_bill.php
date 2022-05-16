<?php
session_start(); 
/****************
	User has clicked pay_bill.
	This page generates his/her bill based on login data
	and displays it.
	User further proceeds to the payment gateway.
 **********************/
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include("../common/header.php");
?>

<h2>Pay bill module</h2>

<?php
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["consumer_loggedin"]) && $_SESSION["consumer_loggedin"] != true){
     header("location: /WBS/consumer/login.php");
    exit;
}

require "../common/db_connection.php";

$query1 = " SELECT 
  consumers.consumer_num
, consumers.name
, consumers.meter_num
, consumers.email
, consumers.address

, meters.reading_date
, GREATEST(
  meters.reading1
, meters.reading2
, meters.reading3
, meters.reading4
, meters.reading5) AS last_reading
, meters.rate

, bills.bill_num
, bills.amount
, bills.due_date
, bills.paid

FROM 
consumers

INNER JOIN
meters

ON
consumers.meter_num = meters.meter_num 

INNER JOIN 
bills

ON
meters.meter_num = bills.meter_num

WHERE
consumers.consumer_num = :consumer_number ";

if($stmt = $pdo->prepare($query1)){
	$stmt->bindParam(":consumer_number", $param_consumer_number, PDO::PARAM_STR);

	$param_consumer_number = $_SESSION["consumer_number"];

	if($stmt->execute()){
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch();
			
	/*		echo "Rows returned ".$stmt->rowCount();
			echo "<br>Consumer name - ". $row["name"].
							"<br>Consumer Number- ". $row["consumer_num"].
							"<br>Meter Number- ". $row["meter_num"].
							"<br>Last reading-". $row["last_reading"] . "units".
							"<br>Reading Date- ". $row["reading_date"].
							"<br>Rate- ". $row["rate"]."per unit".
							"<br>Email - ". $row["email"].
							"<br>Address- ". $row["address"].
							"<br><br>Bill no.- ". $row["bill_num"].
							"<br>Amount-". $row["amount"].
							"<br>Due Date-". $row["due_date"].
							"<br>Paid? -". $row["paid"];
	*/
echo "<table id='pay_bill_consumer'>
	<th colspan = '4'>Consumer Details</th>
	<tr>
		<td>Name</td>
		<td>".$row["name"]."</td>
		<td>Consumer Number</td>
		<td>".$row["consumer_num"]."</td>
	</tr>
	<tr>
		<td> Email</td>
		<td>".$row["email"]."</td>
	</tr>
	<tr>
		<td>Address</td>
		<td>".$row["address"]."</td>
	</tr>
</table>
<br>
<table id='pay_bill_meter'>
	<th colspan = '4'>Meter Details</th>
	<tr>
		<td>Meter Number </td>
		<td>".$row["meter_num"]."</td>
	</tr>
	<tr>
		<td>This Reading</td>
		<td>".$row["last_reading"]." units</td>
		<td>Reading Date</td>
		<td>".$row["reading_date"]."</td>
	</tr>
	<tr>
		<td>Rate of Charge</td>
		<td> Rs. ".$row["rate"]." per unit</td>
	</tr>
	<tr>
		<td> See <a href='previous_reading.php'>Previous readings</a></td>
	</tr>
</table>

<table id='pay_bill_bill'>
	<th colspan = '4' >Billing Details</th>
	<tr>
		<td>Bill Number</td>
		<td>".$row["bill_num"]."</td>
	</tr>
	<tr>
		<td>For the month of</td>
		<td>".date("F", strtotime($row["reading_date"]))."</td>
	</tr>
	<tr>
		<td>Amount Payable</td>
		<td><mark>Rs.".$row["amount"]." </mark></td>
		<td>Due Date</td>
		<td>".$row["due_date"]."</td>
	</tr>
</table>";

echo " <br><a href='https://paypal.me/kpviraj' target='_blank' rel='noopener noreferrer'><button id='pay_button'>Proceed to pay</button></a>";


echo "<br><span id='success'>Requires bank integration.. If you click this now you will be paying me on Paypal. Thanks!</span>";

?>
<?php

			 } else{
				echo "<span id='error'>Your bill has not been generated yet. Try later.</span>";
				}
	} 
	unset($stmt);
}
unset($pdo);

include("../common/footer.php");
?>


