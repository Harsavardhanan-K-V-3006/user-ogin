<?php
require 'vendor/autoload.php';
$conn = new mysqli('localhost', 'root', '', 'userdb1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$mongoDB = $mongoClient->selectDatabase('userdb1');
$usersCollection = $mongoDB->users;
$redisClient = new Redis();
$redisClient->connect('127.0.0.1', 6379);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $pno = $_POST['pno'];
   $select = "SELECT * FROM user_form WHERE email = '$email'";
   $result = mysqli_query($conn, $select);
   if (mysqli_num_rows($result) > 0) {
      echo json_encode(array('success' => false, 'error' => 'User already exists!'));
      exit;
   }
    // Check if user already exists in Redis
    if ($redisClient->exists($email)) {
        echo json_encode(array('success' => false, 'error' => 'User already exists!'));
        exit;
    }
    // Check if user already exists in MongoDB
    $user = $usersCollection->findOne(array('email' => $email));
    if ($user) {
        echo json_encode(array('success' => false, 'error' => 'User already exists!'));
        exit;
    }
    // Check if password and confirm password match
    if ($pass !== $cpass) {
        echo json_encode(array('success' => false, 'error' => 'Passwords do not match!'));
        exit;
    }
    // Prepare and execute the user registration query (MySQL prepared statement)
    $stmt = $conn->prepare("INSERT INTO user_form (name, email, password, age, dob, pno) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $pass, $age, $dob, $pno);
    // Save user data to message.json
    $message = array('name' => $_POST['name'], 
    'email' => $_POST['email'], 
    'password' => $_POST['password'], 
    'age' => $_POST['age'], 
    'dob' => $_POST['dob'], 
    'pno' => $_POST['pno']);
    if (filesize("message.json") == 0) {
        $first_record = array($message);
        $data_to_save = $first_record;
    } else {
        $old_records = json_decode(file_get_contents("message.json"));
        array_push($old_records, $message);
        $data_to_save = $old_records;
    }
    file_put_contents("message.json", json_encode($data_to_save, JSON_PRETTY_PRINT), LOCK_EX);

    if ($stmt->execute()) {
        // Save the email to Redis to mark it as registered
        $redisClient->set($email, 1);
        // Save the user details to MongoDB
        $userData = array('name' => $name, 'email' => $email, 'password' => $pass, 'age' => $age, 'dob' => $dob, 'pno' => $pno);
        $usersCollection->insertOne($userData);
        echo json_encode(array('success' => true));
        exit;
    } 
    else {
        echo json_encode(array('success' => false, 'error' => 'An error occurred during registration.'));
        exit;
    }
}
  
?>