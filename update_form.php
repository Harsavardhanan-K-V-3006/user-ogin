<?php
session_start();
require 'vendor/autoload.php';
$conn = new mysqli('localhost', 'root', '', 'userdb1');
$response = array();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_SESSION['name'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $pno = $_POST['pno'];
    $stmt = $conn->prepare("UPDATE user_form SET age=?, dob=?, pno=? WHERE name=?");
    $stmt->bind_param("issi", $age, $dob, $pno, $name);
    if (strlen(strval($dob)) > 0) {
        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            $response['success'] = true;
            $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
            $userdb = $mongoClient->userdb1;
            $usersCollection = $userdb->users;
            $usersCollection->updateOne(
                ['name' => $name],
                ['$set' => ['age' => $age, 'dob' => $dob, 'pno' => $pno]]
            );
            $redisClient = new Redis();
            $redisClient->connect('localhost', 6379);
            $email = ''; 
            $redisClient->set($email, $name);
        } else {
            $response['success'] = false;
            $response['error'] = 'DATA NOT UPDATED';
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Invalid date of birth';
    }

    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
