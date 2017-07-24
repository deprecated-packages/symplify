<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application;

use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use NetteModule\MicroPresenter;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\PresenterFactory;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\ErrorPresenter;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\SomePresenter;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;

final class PresenterFactoryTest extends TestCase
{
    /**
     * @var IPresenterFactory|PresenterFactory
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

        $this->setupPresenterFactoryMapping();

        $presenterName = 'Some';
        $this->assertSame(
            SomePresenter::class,
            $this->presenterFactory->getPresenterClass($presenterName)
        );
    }

    public function testCreateErrorPresenter(): void
    {
        $this->setupPresenterFactoryMapping();

        $errorPresenter = $this->presenterFactory->createPresenter('Error');
        $this->assertInstanceOf(ErrorPresenter::class, $errorPresenter);

        // @var Presenter $somePresenter
        $this->assertInstanceOf(User::class, $errorPresenter->getUser());
    }

    public function testCreateInvocablePresenter(): void
    {
        $standalonePresenter = $this->presenterFactory->createPresenter(StandalonePresenter::class);
        $this->assertInstanceOf(StandalonePresenter::class, $standalonePresenter);
        $this->assertNotInstanceOf(IPresenter::class, $standalonePresenter);
    }

    private function setupPresenterFactoryMapping(): void
    {
        $this->presenterFactory->setMapping([
            '*' => [
                '',
                '*',
                'Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\*Presenter',
            ],
        ]);
    }
}
