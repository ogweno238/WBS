<?php
session_start();
// -----uncomment to debug---- 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

	// Initialize the session
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["consumer_loggedin"]) || $_SESSION["consumer_loggedin"] !== true){
    header("location: /WBS/consumer/login.php");
    exit;
}

include("../common/header.php");
?>

<h2 style="font-family: 'Parisienne', cursive;">
  Hello! <?php echo $_SESSION["cname"]; ?></h2>

<div class="row">
  <div class="column">
    <a href="my_profile.php">
     <div class="card">
      <h3>My Profile</h3>
      <p>View User Profile Data</p>
    </div>
	</a>
  </div>

  <div class="column">
  	<a href="pay_bill.php">
    <div class="card">
      <h3>Pay Bill</h3>
      <p>Pay your Water bill</p>
    </div>
	</a>
  </div>
  
  <div class="column">
    <a href="add_complaint.php">
    <div class="card">
      <h3>Complaints</h3>
      <p>Complain or Feedbacks</p>
    </div>
	</a>
  </div>
  
  <div class="column">
    <a href="previous_reading.php">
    <div class="card">
      <h3>History</h3>
      <p>See Previous meter data</p>
    </div>
	</a>
  </div>
</div>

<?php include("../common/footer.php") ?>
