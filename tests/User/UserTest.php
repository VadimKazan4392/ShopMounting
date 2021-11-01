<?php


class UserTest  extends \PHPUnit\Framework\TestCase
{
    private $user;

    protected function setUp(): void
    {
        $this->user = new \App\User();
        $this->user->setAge(33);
    }

    protected function tearDown(): void
    {

    }

    public function testAge()
    {
        $this->assertEquals(33, $this->user->getAge());
    }
}