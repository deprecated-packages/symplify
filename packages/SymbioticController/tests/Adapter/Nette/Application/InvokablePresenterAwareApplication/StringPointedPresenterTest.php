<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\InvokablePresenterAwareApplication;

use Nette\Application\Application;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\InvokablePresenterAwareApplication;
use Symplify\SymbioticController\Adapter\Nette\Application\PresenterFactory;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\SomePresenter;

final class StringPointedPresenterTest extends TestCase
{
    /**
     * @var RouteList
     */
    private $router;

    /**
     * @var InvokablePresenterAwareApplication
     */
    private $application;

    /**
     * @var PresenterFactory
     */
    private $presenterFactory;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../config.neon');
        $this->router = $container->getByType(IRouter::class);
        $this->application = $container->getByType(Application::class);
        $this->presenterFactory = $container->getByType(PresenterFactory::class);

        $this->prepareRouter();
        $this->preparePresenterFactory();
    }

    public function test()
    {
        $httpRequest = $this->createHttpRequestWithUrl('https://domain.com/hi');
        $applicationRequest = $this->router->match($httpRequest);

        ob_start();
        $this->application->processRequest($applicationRequest);
        $responseOutput = ob_get_clean();
        $this->assertSame('Hi', $responseOutput);

        $this->assertInstanceOf(SomePresenter::class, $this->application->getPresenter());
    }

    private function createHttpRequestWithUrl(string $url): Request
    {
        return new Request(new UrlScript($url));
    }

    private function prepareRouter(): void
    {
        $this->router[] = new Route('/hi', 'Some:default');
    }

    private function preparePresenterFactory(): void
    {
        $this->presenterFactory->setMapping([
            '*' => [
                '',
                '*',
                'Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\*Presenter'
            ],
        ]);
    }
}
