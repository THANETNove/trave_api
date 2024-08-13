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
        $check_sql = "SELECT view FROM press_view_likes WHERE id_travel = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $input_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // ID exists, get the current view value
            $row = $check_result->fetch_assoc();
            $current_view = $row['view'];

            // Determine the new view value
            if (is_null($current_view)) {
                $new_view = 1;
            } else {
                $new_view = $current_view + 1;
            }

            // Update the view count
            $update_sql = "UPDATE press_view_likes SET view = ? WHERE id_travel = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $new_view, $input_id);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo json_encode(['message' => 'View count updated successfully!']);
            } else {
                echo json_encode(['error' => 'Failed to update view count!']);
            }
        } else {
            // ID does not exist, insert a new record with view = 1
            $insert_sql = "INSERT INTO press_view_likes (id_travel, view) VALUES (?, 1)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("s", $input_id);
            $insert_stmt->execute();

            if ($insert_stmt->affected_rows > 0) {
                echo json_encode(['message' => 'New record created with view = 1!']);
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
