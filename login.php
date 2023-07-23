<?php
// login.php
require 'vendor/autoload.php';
session_start();
$conn = new mysqli('localhost', 'root', '', 'userdb1');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Using prepared statements to prevent SQL injection for MySQL
    $stmt = $conn->prepare("SELECT * FROM user_form WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['name'] = $row['name'];

        // MongoDB connection
        $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
        $userdb = $mongoClient->userdb1;
        $usersCollection = $userdb->login;

        // Assuming 'users' collection in 'userdb1' database for MongoDB
        // Inserting the user data into MongoDB
        $usersCollection->insertOne([
            'name' => $_SESSION['name'],
            'email' => $email,
            'password' => $pass
        ]);

        // Redis connection
        $redisClient = new Redis();
        $redisClient->connect('localhost', 6379);

        // Assuming the Redis key is the email and the value is the user's name
        $redisClient->set($email, $_SESSION['name']);

        echo json_encode(array('success' => true)); // Return success response
    } else {
        echo json_encode(array('success' => false, 'error' => 'Incorrect email or password!')); // Return error response
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
