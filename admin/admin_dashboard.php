<?php 
session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: /WBS/admin/login.php");
    exit;
}

include("../common/header.php");
?>

<h2>Hi! You're logged in as <?php echo $_SESSION["username"]; ?></h2>

<div class="row">
  <div class="column">
    <a href="add_consumer.php">
     <div class="card">
      <h3>Add Consumer</h3>
      <p>Register new consumer</p>
    </div>
	</a>
  </div>

  <div class="column">
  	<a href="meter_update.php">
    <div class="card">
      <h3>Meter Update</h3>
      <p>Update meter readings</p>
    </div>
	</a>
  </div>
  
  <div class="column">
    <a href="review.php">
    <div class="card">
      <h3>Review</h3>
      <p>Search and View various data</p>
    </div>
	</a>
  </div>
  
  <div class="column">
    <a href="add_admin.php">
    <div class="card">
      <h3>Add Admin</h3>
      <p>Make new admin</p>
    </div>
	</a>
  </div>
</div>
<?php include("../common/footer.php") ?>
