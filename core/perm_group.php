<?php

include_once('../core/initialize.php');

class Perm_group
{
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }
    public function group_exists($groupId)
    {
        $query = 'SELECT COUNT(*) FROM perm_groups WHERE id = ' . $groupId;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            return false;
        }
        return true;
    }

    public function read($groupId)
    {
        $query = 'SELECT * FROM users_groups WHERE group_id = ' . $groupId . '';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
