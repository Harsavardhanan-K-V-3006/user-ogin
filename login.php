<?php
// login.php

session_start();
$conn = mysqli_connect('localhost', 'root', '', 'userdb1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $email = $_POST['email'];
   $pass = $_POST['password'];

   $select = "SELECT * FROM user_form WHERE email = '$email' && password = '$pass'";
   $result = mysqli_query($conn, $select);

   if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result);
      $_SESSION['name'] = $row['name'];
      echo json_encode(array('success' => true)); // Return success response
      exit;
   } else {
      echo json_encode(array('success' => false, 'error' => 'Incorrect email or password!')); // Return error response
      exit;
   }
}
?>
