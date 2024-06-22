<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

require_once 'core\initialize.php';

class User_test extends TestCase
{

    private User $user;
    private MockObject $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->user = new User($this->db);
    }

    public function testRead()
    {
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchAll')->willReturn([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ]);

        $this->db->method('prepare')->willReturn($statement);
        $result = $this->user->read();
        $this->assertInstanceOf(PDOStatement::class, $result);
        $this->assertEquals([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $result->fetchAll());
    }

    public function testUserExists()
    {
        $userId = 1;
        $expectedQuery = 'SELECT COUNT(*) FROM users WHERE id = ' . $userId;
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);

        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($statement);

        $result = $this->user->user_exists($userId);
        $this->assertTrue($result);
    }

    public function testUserDoesNotExists()
    {
        $userId = 1;
        $expectedQuery = 'SELECT COUNT(*) FROM users WHERE id = ' . $userId;
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(0);

        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($statement);

        $result = $this->user->user_exists($userId);
        $this->assertFalse($result);
    }

    public function testUserRights()
    {
        $userId = 1;
        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //разрешения
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchAll')->willReturn(
            [
                ['permission' => 'send_messages'],
                ['permission' => 'service_api'],
                ['permission' => 'debug']
            ]
        );
        //баны
        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')->willReturn(true);
        $statement2->method('fetchAll')->willReturn(
            [['permission' => 'debug']]
        );

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);


        $expectedPermissions = array(
            "send_messages" => true,
            "service_api" => true,
            "debug" => false
        );

        $result = $this->user->user_rights($userId);
        $this->assertEquals($expectedPermissions, $result);
    }
    public function testUserRightsNoUser()
    {
        $userId = 999;
        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(0);
        //разрешения
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchAll')->willReturn(
            [
                ['permission' => 'send_messages'],
                ['permission' => 'service_api'],
                ['permission' => 'debug']
            ]
        );
        //баны
        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')->willReturn(true);
        $statement2->method('fetchAll')->willReturn(
            [['permission' => 'debug']]
        );

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);


        $expectedArray = array(
            'error' => 'No such user'
        );

        $result = $this->user->user_rights($userId);
        $this->assertEquals($expectedArray, $result);
    }

    public function testUserRightsNoGroups()
    {
        $userId = 1;
        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //разрешения
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchAll')->willReturn(
            []
        );
        //баны
        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')->willReturn(true);
        $statement2->method('fetchAll')->willReturn(
            []
        );

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);


        $expectedArray =
            array(
                "send_messages" => false,
                "service_api" => false,
                "debug" => false
            );

        $result = $this->user->user_rights($userId);
        $this->assertEquals($expectedArray, $result);
    }

    public function testAddUserToGroup()
    {
        $userId = 1;
        $groupId = 1;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(1);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->add_user_to_group($userId, $groupId);
        $this->assertTrue($result);
    }

    public function testAddUserToGroupNoUser()
    {
        $userId = 999;
        $groupId = 1;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(0);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(1);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->add_user_to_group($userId, $groupId);
        $this->assertFalse($result);
    }

    public function testAddUserToGroupNoGroup()
    {
        $userId = 1;
        $groupId = 999;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(0);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->add_user_to_group($userId, $groupId);
        $this->assertFalse($result);
    }

    public function testRemoveUserFromGroup()
    {
        $userId = 1;
        $groupId = 1;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(1);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->remove_user_from_group($userId, $groupId);
        $this->assertTrue($result);
    }

    public function testRemoveUserFromGroupNoUser()
    {
        $userId = 999;
        $groupId = 1;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(0);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(1);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->remove_user_from_group($userId, $groupId);
        $this->assertFalse($result);
    }

    public function testRemoveUserFromGroupNoGroup()
    {
        $userId = 1;
        $groupId = 999;

        //будет запрос во время !$this->user_exists($userId)
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);
        //будет запрос во время !$perm_group->group_exists($groupId)
        $statement1 = $this->createMock(PDOStatement::class);
        $statement1->method('execute')->willReturn(true);
        $statement1->method('fetchColumn')->willReturn(0);

        $statement2 = $this->createMock(PDOStatement::class);
        $statement2->method('execute')
            ->willReturn(true);

        $this->db->expects($this->atLeastOnce())
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($statement, $statement1, $statement2);

        $result = $this->user->remove_user_from_group($userId, $groupId);
        $this->assertFalse($result);
    }
}
