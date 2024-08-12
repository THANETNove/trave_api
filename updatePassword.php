<?php
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // รับค่า id และ new_password จากการร้องขอ
    $id = $_POST['id'];
    $new_password = $_POST['password'];
    // แฮชรหัสผ่านใหม่ก่อนบันทึก
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // อัปเดตรหัสผ่านในฐานข้อมูล
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_password, $id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Password updated successfully!']);
    } else {
        echo json_encode(['error' => 'Failed to update password!']);
    }

    $stmt->close();
}

$conn->close();