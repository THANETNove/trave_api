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

        // Check if the ID exists in the press_view_likes table
        $check_sql = "SELECT `like` FROM press_view_likes WHERE id_travel = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $input_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // ID exists, get the current like value
            $row = $check_result->fetch_assoc();
            $current_like = $row['like'];

            // Determine the new like value
            if (is_null($current_like)) {
                $new_like = 1;
            } else {
                $new_like = $current_like + 1;
            }

            // Update the like count
            $update_sql = "UPDATE press_view_likes SET `like` = ? WHERE id_travel = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $new_like, $input_id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo json_encode(['message' => 'Like count updated successfully!']);
            } else {
                echo json_encode(['error' => 'Failed to update like count!']);
            }
        } else {
            // ID does not exist, insert a new record with like = 1
            $insert_sql = "INSERT INTO press_view_likes (id_travel, `like`) VALUES (?, 1)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("s", $input_id);
            $insert_stmt->execute();

            if ($insert_stmt->affected_rows > 0) {
                echo json_encode(['message' => 'New record created with like = 1!']);
            } else {
                echo json_encode(['error' => 'Failed to create new record!']);
            }
        }

        $check_stmt->close();
        $update_stmt->close();
        $insert_stmt->close();
    } else {
        echo json_encode(['error' => 'Invalid request!']);
    }
}

$conn->close();
