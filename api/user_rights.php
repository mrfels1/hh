<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = new User($db);
    if (isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];
        $result = $user->user_rights($userId);
        if (!isset($result['error'])) {
            $resultArr = array();
            $resultArr['data'] = array();
            array_push($resultArr['data'], $result);
            http_response_code(200);
            echo json_encode($resultArr);
        } else {
            http_response_code(500);
            echo json_encode($result);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
