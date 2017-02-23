<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Blameable;

use Nette\Security\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Zenify\DoctrineBehaviors\Blameable\UserCallable;

final class UserCallableTest extends TestCase
{
    /**
     * @var ObjectProphecy|User
     */
    private $userMock;

    /**
     * @var UserCallable
     */
    private $userCallable;

    protected function setUp(): void
    {
        $this->userMock = $this->prophesize(User::class);
        $this->userMock->getId()->willReturn(1);
        $this->userMock->isLoggedIn()->willReturn(true);
        $this->userCallable = new UserCallable($this->userMock->reveal());
    }

    public function testInvoke(): void
    {
        $this->assertSame(1, call_user_func($this->userCallable));

        $this->userMock->isLoggedIn()->willReturn(false);
        $this->assertSame(null, call_user_func($this->userCallable));
    }
}
