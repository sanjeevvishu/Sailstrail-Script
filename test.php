<?php
//phpinfo();

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$servername = "localhost";
$username = "sailsorgzit";
$password = "deEv!#sails@2021";
$database = 'sailsorgzit';

// Create connection
$conn = new mysqli($servername, $username, $password,$database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "database Connected successfully";