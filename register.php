<?php
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจาก POST
    $name = $_POST['first_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $subdistrict = $_POST['subdistrict'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $zipCode = $_POST['zip_code'];
    $phone = $_POST['phone'];

    // ตรวจสอบความซ้ำซ้อนของ $name
    $checkNameSql = "SELECT * FROM users WHERE name = ?";
    $stmt = $conn->prepare($checkNameSql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $nameResult = $stmt->get_result();

    // ตรวจสอบความซ้ำซ้อนของ $email
    $checkEmailSql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $emailResult = $stmt->get_result();

    // ตรวจสอบผลลัพธ์
    $errors = [];

    if ($nameResult->num_rows > 0) {
        $errors['name_exists'] = 'Name already exists';
    }
    if ($emailResult->num_rows > 0) {
        $errors['email_exists'] = 'Email already exists';
    }

    if (!empty($errors)) {
        echo json_encode(['error' => $errors]);
    } else {
        // เตรียมคำสั่ง INSERT
        $sql = "INSERT INTO users (name, email, password, address, subdistrict, district, province, zipCode, phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $name, $email, $password, $address, $subdistrict, $district, $province, $zipCode, $phone);

        if ($stmt->execute()) {
            // รับ ID ของ record ที่เพิ่งเพิ่ม
            $lastId = $conn->insert_id;

            // ดึงข้อมูลที่บันทึกกลับ
            $query = "SELECT * FROM users WHERE id = $lastId";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode(['message' => 'New record created successfully', 'data' => $row]);
            } else {
                echo json_encode(['error' => 'Error retrieving the record']);
            }
        } else {
            echo json_encode(['error' => 'Error: ' . $stmt->error]);
        }
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
