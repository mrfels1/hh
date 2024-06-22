<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

require_once 'core\initialize.php';

class Perm_group_test extends TestCase
{

    private Perm_group $perm_group;
    private MockObject $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->perm_group = new Perm_group($this->db);
    }

    public function testRead()
    {
        $groupId = 1;
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchAll')->willReturn([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ]);

        $this->db->method('prepare')->willReturn($statement);
        $result = $this->perm_group->read($groupId);
        $this->assertInstanceOf(PDOStatement::class, $result);
        $this->assertEquals([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $result->fetchAll());
    }

    public function testGroupExists()
    {
        $groupId = 1;
        $expectedQuery = 'SELECT COUNT(*) FROM perm_groups WHERE id = ' . $groupId;
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);

        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($statement);

        $result = $this->perm_group->group_exists($groupId);
        $this->assertTrue($result);
    }

    public function testGroupDoesNotExists()
    {
        $groupId = 999;
        $expectedQuery = 'SELECT COUNT(*) FROM perm_groups WHERE id = ' . $groupId;
        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn(0);

        $this->db->expects($this->once())
            ->method('prepare')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($statement);

        $result = $this->perm_group->group_exists($groupId);
        $this->assertFalse($result);
    }
}
