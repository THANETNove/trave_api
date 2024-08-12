<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "traverWeb";
/* include 'db_config.php'; */

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['isAdd']) && $_POST['isAdd'] == 'true') {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Fetch user with given username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($input_password, $user['password'])) {
            unset($user['password']); // Removing password from the response for security reasons
            echo json_encode(['message' => 'Login successful!', 'user' => $user]);
        } else {
            echo json_encode(['error' => 'Incorrect password!']);
        }
    } else {
        echo json_encode(['error' => 'Login not found!']);
    }
} else {
    echo json_encode(['error' => 'Invalid request!']);
}

$conn->close();