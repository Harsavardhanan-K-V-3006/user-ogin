<?php
// user_data.php

session_start();

if (!isset($_SESSION['name'])) {
   header('HTTP/1.1 401 Unauthorized');
   exit;
}

// Add your database connection and other necessary functions here
$conn = mysqli_connect('localhost', 'root', '', 'userdb1');

$name = $_SESSION['name'];

// Retrieve user information from the database
$select = "SELECT name, age, dob, pno FROM user_form WHERE name='$name'";
$result = $conn->query($select);

if ($result->num_rows > 0) {
   $data = $result->fetch_assoc();
   echo json_encode($data);
} else {
   header('HTTP/1.1 404 Not Found');
}
