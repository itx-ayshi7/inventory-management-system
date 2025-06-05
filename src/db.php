<?php 
$conn = new mysqli("localhost", "root", "", "InvexaPlus"); // Make sure the DB name is correct
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>