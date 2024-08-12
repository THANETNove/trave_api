<?php
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['isAdd']) && $_POST['isAdd'] == 'true') {
        $input_email = $_POST['email'];
        // Fetch user with given email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $input_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // If user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['message' => 'Email found!', 'user' => $user]);
        } else {
            echo json_encode(['error' => 'Email not found!', 'user' => 'null']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request!']);
    }
}

$conn->close();
