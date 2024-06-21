<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');

$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (isset($_REQUEST['user_id']) && isset($_REQUEST['group_id'])) {
        $userId = $_REQUEST['user_id'];
        $groupId = $_REQUEST['group_id'];

        if ($user->remove_user_from_group($userId, $groupId)) {
            http_response_code(200);
            echo json_encode(['message' => 'User removed to group successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to remove user from group']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id or group_id']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
