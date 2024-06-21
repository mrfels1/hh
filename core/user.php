<?php

include_once('../core/initialize.php');

class User
{
    private $connection;


    public function __construct($db)
    {
        $this->connection = $db;
    }

    private function user_exists($userId)
    {
        $query = 'SELECT COUNT(*) FROM users WHERE id = ' . $userId;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            return false;
        }
        return true;
    }

    public function read()
    {
        $query = 'SELECT * FROM users';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function user_rights($userId)
    {
        if (!$this->user_exists($userId)) {
            return false;
        }
        $userRightsArray = array(
            "send_messages" => false,
            "service_api" => false,
            "debug" => false
        );
        $query = 'SELECT DISTINCT p.permission FROM permissions p
            JOIN groups_permissions gp ON p.permission = gp.permission
            JOIN users_groups ug ON gp.group_id = ug.group_id
            JOIN perm_groups pg ON ug.group_id = pg.id
            WHERE ug.user_id = ' . $userId . ' 
            AND pg.is_ban_group = 0';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            switch ($row['permission']) {
                case 'send_messages':
                    $userRightsArray['send_messages'] = true;
                    break;
                case 'service_api':
                    $userRightsArray['service_api'] = true;
                    break;
                case 'debug':
                    $userRightsArray['debug'] = true;
                    break;
            }
        }

        $query = 'SELECT DISTINCT p.permission FROM permissions p
            JOIN groups_permissions gp ON p.permission = gp.permission
            JOIN users_groups ug ON gp.group_id = ug.group_id
            JOIN perm_groups pg ON ug.group_id = pg.id
            WHERE ug.user_id = ' . $userId . ' 
            AND pg.is_ban_group = 1';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            switch ($row['permission']) {
                case 'send_messages':
                    $userRightsArray['send_messages'] = false;
                    break;
                case 'service_api':
                    $userRightsArray['service_api'] = false;
                    break;
                case 'debug':
                    $userRightsArray['debug'] = false;
                    break;
            }
        }

        return $userRightsArray;
    }
    public function add_user_to_group($userId, $groupId)
    {
        if (!$this->user_exists($userId)) {
            return false;
        }
        $perm_group = new Perm_group($this->connection);
        if (!$perm_group->group_exists($groupId)) {
            return false;
        }

        $query = 'INSERT IGNORE INTO users_groups (user_id, group_id) VALUES (' . $userId . ',' . $groupId . ')';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        if (!$stmt) {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
            return false;
        }
        return true;
    }

    public function remove_user_from_group($userId, $groupId)
    {
        if (!$this->user_exists($userId)) {
            return false;
        }
        $perm_group = new Perm_group($this->connection);
        if (!$perm_group->group_exists($groupId)) {
            return false;
        }

        $query = 'DELETE FROM users_groups WHERE user_id = ' . $userId . ' AND group_id = ' . $groupId;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        if (!$stmt) {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
            return false;
        }
        return true;
    }
}
