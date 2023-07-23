<?php
// update.php

session_start();
$conn = mysqli_connect('localhost', 'root', '', 'userdb1');
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $name = $_SESSION['name'];
   $age = $_POST['age'];
   $dob = $_POST['dob'];
   $pno = $_POST['pno'];

   if (strlen(strval($dob)) > 0) {
      $select = "UPDATE user_form SET age=$age, dob='$dob', pno=$pno WHERE name='$name' ";

      if ($conn->query($select) === TRUE) {
         $_SESSION['name'] = $name;
         $response['success'] = true;
      } else {
         $response['success'] = false;
         $response['error'] = 'DATA NOT UPDATED';
      }
   } else {
      $response['success'] = false;
      $response['error'] = 'Invalid date of birth';
   }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
