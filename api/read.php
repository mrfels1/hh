<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = new User($db);
    $result = $user->read();

    $num = $result->rowCount();
    if ($num > 0) {

        $resultArr = array();
        $resultArr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $userItem = array(
                'userid' => $id
            );
            array_push($resultArr['data'], $userItem);
        }
        echo json_encode($resultArr);
    } else {
        echo json_encode(['message' => 'No Users Found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
