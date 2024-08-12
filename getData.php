<?php
header("Content-Type: application/json; charset=UTF-8");

include 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['isAdd']) && $_POST['isAdd'] == 'true') {
        $input_id = $_POST['id'];

        // ดึงข้อมูลทั้งหมดที่ category ตรงกับ id
        $sql = "SELECT * FROM traves WHERE category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $input_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้ามีข้อมูลที่ตรงกับ category
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row; // เก็บข้อมูลแต่ละแถวใน array
            }
            echo json_encode(['message' => 'Category found!', 'data' => $data]);
        } else {
            echo json_encode(['error' => 'Category not found!', 'data' => []]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request!']);
    }
}

$conn->close();
