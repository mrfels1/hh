<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');

$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['group_id'])) {
        $userId = $_POST['user_id'];
        $groupId = $_POST['group_id'];

        if ($user->add_user_to_group($userId, $groupId)) {
            http_response_code(200);
            echo json_encode(['message' => 'User added to group successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add user to group']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id or group_id']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
