<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $group = new Perm_group($db);
    if (isset($_GET['group_id'])) {
        $userId = $_GET['group_id'];
        $result = $group->read($userId);
        if ($result) {
            $resultArr = array();
            $resultArr['data'] = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $userItem = array(
                    'userid' => $user_id
                );
                array_push($resultArr['data'], $userItem);
            }
            http_response_code(200);
            echo json_encode($resultArr);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'No such group']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing group_id']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
