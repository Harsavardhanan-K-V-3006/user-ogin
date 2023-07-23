<?php
// admin_form.php

session_start();

// Add your database connection and other necessary functions here
$conn = mysqli_connect('localhost', 'root', '', 'userdb1');

if (!isset($_SESSION['name'])) {
   header('location: login_form.php');
   exit;
}

$name = $_SESSION['name'];
$_SESSION['name']=$name;
// Retrieve user information from the database
$select = "SELECT * FROM user_form WHERE name='$name'";
$result = $conn->query($select);
if ($result->num_rows > 0) {
   $data = $result->fetch_assoc();
   $age = $data['age'];
   $dob = $data['dob'];
   $pno = $data['pno'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>
   <!-- custom css file link  -->
   <link rel="stylesheet" href="./css/admin_form.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
   <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
   <div class="container">
      <br>
      <div class="content">
         <h1>welcome <span><?php echo $_SESSION['name'] ?></span></h1>
         <a href="logout.php" class="btn btn-lg">logout</a>
         <br>
         <br>
         <label for="">AGE:</label> <span><?php echo $age; ?></span>
         <br>
         <label for="">DATE OF BIRTH:</label> <span><?php echo $dob; ?></span>
         <br>
         <label for="">CONTACT NO:</label><span><?php echo $pno; ?></span>
         <br>
         <br><a href="update_form.html" class="btn btn-lg">Update Profile</a>
      </div>
   </div>
</body>

</html>
