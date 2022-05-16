<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'postgres');
define('DB_PASSWORD', 'toor');
define('DB_NAME', 'water_billing');
 
/* Attempt to connect to database */
try{
    $pdo = new PDO("pgsql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
