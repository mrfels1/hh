<?php

include_once('../core/initialize.php');

class Perm_group
{
    /** @var PDO */
    private $connection;

    /**
     * Конструктор для класса.
     *
     * @param PDO $db Соединение с базой данных.
     */
    public function __construct($db)
    {
        $this->connection = $db;
    }

    /**
     * Проверяет, существует ли группа с указанным идентификатором в таблице 'perm_groups'.
     *
     * @param int $groupId Идентификатор группы для проверки.
     * @return bool Возвращает true, если группа существует, иначе false.
     */
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

    /**
     * Считывает пользователей, связанных с конкретной группой из базы данных.
     *
     * @param int $groupId Идентификатор группы, для которой нужно получить пользователей.
     * @return PDOStatement Подготовленное выражение, представляющее результат запроса.
     */
    public function read($groupId)
    {
        $query = 'SELECT * FROM users_groups WHERE group_id = ' . $groupId . '';
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
