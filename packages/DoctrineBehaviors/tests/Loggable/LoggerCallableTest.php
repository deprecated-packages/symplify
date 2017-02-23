<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Loggable;

use PHPUnit\Framework\TestCase;
use Zenify\DoctrineBehaviors\Contract\Loggable\LoggerInterface;
use Zenify\DoctrineBehaviors\Loggable\LoggerCallable;

class LoggerCallableTest extends TestCase
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var LoggerCallable
     */
    private $loggerCallable;

    protected function setUp(): void
    {
        $loggerMock = $this->prophesize(LoggerInterface::class);
        $that = $this;
        $loggerMock->process('message')->will(function ($args) use ($that) {
            $that->message = 'someMessage';
        });
        $this->loggerCallable = new LoggerCallable($loggerMock->reveal());
    }

    public function testInvoke(): void
    {
        $loggerCallable = $this->loggerCallable;
        $loggerCallable('message');
        $this->assertSame('someMessage', $this->message);
    }
}
