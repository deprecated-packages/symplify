<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application;

use Nette\Application\IPresenterFactory;
use NetteModule\MicroPresenter;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\PresenterFactory;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\SomePresenter;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;

final class PresenterFactoryTest extends TestCase
{
    /**
     * @var PresenterFactory
     */
    private $presenterFactory;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../config.neon'
        );

        $this->presenterFactory = $container->getByType(IPresenterFactory::class);
    }

    public function testFactoryWasReplaced(): void
    {
        $this->assertInstanceOf(PresenterFactory::class, $this->presenterFactory);
    }

    public function testGetPresenterClassForClass(): void
    {
        $presenterName = StandalonePresenter::class;

        $this->assertSame(
            StandalonePresenter::class,
            $this->presenterFactory->getPresenterClass($presenterName)
        );
    }

    public function testGetPresenterClassForString(): void
    {
        $presenterName = 'Nette:Micro';
        $this->assertSame(
            MicroPresenter::class,
            $this->presenterFactory->getPresenterClass($presenterName)
        );

        $this->presenterFactory->setMapping([
            '*' => [
                '',
                '*',
                'Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\*Presenter'
            ],
        ]);

        $presenterName = 'Some';
        $this->assertSame(
            SomePresenter::class,
            $this->presenterFactory->getPresenterClass($presenterName)
        );
    }
}
