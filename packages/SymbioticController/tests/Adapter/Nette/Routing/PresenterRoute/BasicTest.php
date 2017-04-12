<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing\PresenterRoute;

use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\SomePresenter;

final class BasicTest extends TestCase
{
    /**
     * @expectedException \Symplify\SymbioticController\Exception\MissingClassException
     */
    public function testMissingClass()
    {
        new PresenterRoute('/some-path-mask', 'not-invokable-presenter');
    }

    /**
     * @expectedException \Symplify\SymbioticController\Exception\MissingInvokeMethodException
     */
    public function testMissingInvokeMethod()
    {
        new PresenterRoute('/some-path-mask', SomePresenter::class);
    }
}
