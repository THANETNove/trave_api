<?php
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['first_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $subdistrict = $_POST['subdistrict'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $zipCode = $_POST['zip_code'];
    $phone = $_POST['phone'];


    $sql = "INSERT INTO users (name, email,password,address,subdistrict,district,province,zipCode,phone) VALUES ('$name', '$email' ,'$password','$address','$subdistrict','$district','$province','$zipCode','$phone')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'New record created successfully']);
    } else {
        echo json_encode(['error' => 'Error: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
