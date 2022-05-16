<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
Update meter data and bill data
*/

	// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /WBS/admin/login.php");
    exit;
}

require_once"../common/db_connection.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

/////////insert Meter_num, reading_date and rate in meters////////////////////////
    //for meter data being inserted first time..
    //This meter_num doesn't exist in meters db yet. Thus this.
	$query = "INSERT INTO meters(meter_num, reading_date, rate) 
                VALUES (:meter_number, :reading_date, :rate) 
                ON CONFLICT (meter_num) DO NOTHING; ";

	if($checkstmt = $pdo->prepare($query)){
		$checkstmt->bindParam(":meter_number", $param_meter_number, PDO::PARAM_STR);
        $checkstmt->bindParam(":reading_date", $param_reading_date, PDO::PARAM_STR);
        $checkstmt->bindParam(":rate", $param_rate, PDO::PARAM_STR);

        $param_meter_number = trim($_POST["meter_number"]);
        $param_reading_date = trim($_POST["reading_date"]);
        $param_rate = trim($_POST["rate"]);

        try{
		if($checkstmt->execute()){
				// echo "Meter_num, reading_date and rate have been inserted.<br> NOw inserting reading..<br>";

// /////////////////update reading data in meters////////////////////////
 /*For below query look here --
 https://stackoverflow.com/questions/60590716/update-minimum-value-column-of-selected-rows/60590778#60590778
*/
    //meters db already has this meter_num
    //now we only need to update the reading as the case may be. Thus this.
    $sql = "UPDATE meters
    SET reading1 = (case least(reading1, reading2, reading3, reading4, reading5)
                         when reading1 then :reading
                         else reading1
                   end),
        reading2 = (case least(reading1, reading2, reading3, reading4, reading5)
                         when reading1 then reading2
                         when reading2 then :reading
                         else reading2
                    end),
        reading3 = (case least(reading1, reading2, reading3, reading4, reading5)
                         when reading1 then reading3
                         when reading2 then reading3
                         when reading3 then :reading
                         else reading3
                    end),
       reading4 = (case least(reading1, reading2, reading3, reading4, reading5)
                         when reading1 then reading4
                         when reading2 then reading4
                         when reading3 then reading4
                         when reading4 then :reading
                         else reading4
                    end),
      reading5 = (case least(reading1, reading2, reading3, reading4, reading5)
                        when reading1 then reading5
                        when reading2 then reading5
                        when reading3 then reading5
                        when reading4 then reading5
                        when reading5 then :reading
                        else reading5
                     end),
        rate = :rate
WHERE meter_num = :meter_number ";

   // Prepare an update statement
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
             $stmt->bindParam(":meter_number", $param_meter_number, PDO::PARAM_STR);
             $stmt->bindParam(":reading", $param_reading, PDO::PARAM_STR);
             $stmt->bindParam(":rate", $param_rate, PDO::PARAM_STR);

            $param_meter_number = trim($_POST["meter_number"]);
            $param_reading = trim($_POST["reading"]);
            $param_rate = trim($_POST["rate"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                echo "<span id='success'>Reading Updated!</span> ";
                
////////////////////////update bills start///////////////////
$sql = " INSERT INTO bills(meter_num, amount, due_date, paid) 
            VALUES (:meter_number, :amount, :due_date, false) 
            ON CONFLICT (meter_num)
            DO UPDATE 
            SET amount = :amount,
                due_date = :due_date,
                 paid = false ";
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":meter_number", $param_meter_number, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt->bindParam(":due_date", $param_due_date, PDO::PARAM_STR);

            $param_amount = $param_reading * $param_rate;
            $param_reading = trim($_POST["meter_number"]);
            //$param_due_date = trim($_POST["reading_date"]); //just testing it with reading date.

            $time = strtotime($_POST["reading_date"]);
            $param_due_date = date("m-d-Y", strtotime("+1 month", $time));
            
            if($stmt->execute()){
                echo "<br><span id='success'>Bill generated and saved.</span>";
            }

        }
                ////////update bills end/////

            } else{
                echo "<span id='error'>Something went wrong. Please try again later.</span>";
            }

            // Close statement
            unset($stmt);
        }

		}
        
    }//try close 
    catch(PDOException $e){
    echo "<span id='error'>Consumer with this meter number has not been registered yet.</span>".
        " <a href='add_consumer.php'>Add consumer</a> first.<br><br>";
        
    // die("---------For the Developer--------".
    //     "<br>error inserting " . $e->getMessage());
    }
     unset($checkstmt);
	}



    // Close connection
    unset($pdo);
}

include("../common/header.php"); 
?>

You're logged in as  
 <?php echo htmlspecialchars($_SESSION["username"]); ?>

	<h2> Meter Updation Module </h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

		<input type="number" name="meter_number" required="true" placeholder="Meter Number" autofocus="true"><br/>

        <input type="date" name="reading_date" required="true" placeholder="Reading Date"><br/>

         <input type="number" name="reading" step="0.01" min="0.01" required="true" placeholder="Reading"><br/>

		<input type="number" name="rate" list="rateList" step="0.01" min="0.01" required="true" placeholder="Rate">
					<datalist id="rateList">
 					 	<option value="0.99">  
  						<option value="0.55">
					</datalist>
				 <br/><br/>

                  <input type="submit">
	</form>

<?php include("../common/footer.php"); ?>