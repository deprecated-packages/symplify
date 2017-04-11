<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\InvokablePresenterAwareApplication;

use Nette\Application\Application;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use OdbavTo\PresenterRoute\Route as PresenterRoute;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\InvokablePresenterAwareApplication;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;

final class ClassPointedPresenterTest extends TestCase
{
    /**
     * @var RouteList
     */
    private $router;

    /**
     * @var InvokablePresenterAwareApplication
     */
    private $application;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../config.neon');
        $this->router = $container->getByType(IRouter::class);
        $this->application = $container->getByType(Application::class);

        $this->prepareRouter();
    }

    public function test()
    {
        $httpRequest = $this->createHttpRequestWithUrl('https://domain.com/hi');
        $applicationRequest = $this->router->match($httpRequest);

        ob_start();
        $this->application->processRequest($applicationRequest);
        $responseOutput = ob_get_clean();
        $this->assertSame('Hey', $responseOutput);

        $this->assertInstanceOf(StandalonePresenter::class, $this->application->getPresenter());
    }

    private function createHttpRequestWithUrl(string $url): Request
    {
        return new Request(new UrlScript($url));
    }

    private function prepareRouter(): void
    {
        $this->router[] = new PresenterRoute('/hi', StandalonePresenter::class);
    }
}
