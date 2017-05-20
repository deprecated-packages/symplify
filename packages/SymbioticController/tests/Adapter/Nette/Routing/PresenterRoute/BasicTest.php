<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing\PresenterRoute;

use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;
use Symplify\SymbioticController\Exception\MissingClassException;
use Symplify\SymbioticController\Exception\MissingInvokeMethodException;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\SomePresenter;

final class BasicTest extends TestCase
{
    public function testMissingClass(): void
    {
        $this->expectException(MissingClassException::class);
        new PresenterRoute('/some-path-mask', 'not-invokable-presenter');
    }

    public function testMissingInvokeMethod(): void
    {
        $this->expectException(MissingInvokeMethodException::class);
        new PresenterRoute('/some-path-mask', SomePresenter::class);
    }
}
