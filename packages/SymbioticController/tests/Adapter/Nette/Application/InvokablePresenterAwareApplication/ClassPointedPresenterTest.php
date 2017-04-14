<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Application\InvokablePresenterAwareApplication;

use Nette\Application\Application;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;
use Symplify\SymbioticController\Adapter\Nette\Application\InvokablePresenterAwareApplication;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\TemplateRendererPresenter;

final class ClassPointedPresenterTest extends TestCase
{
    /**
     * @var IRouter|RouteList
     */
    private $router;

    /**
     * @var InvokablePresenterAwareApplication|Application
     */
    private $application;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../config.neon');
        $this->router = $container->getByType(IRouter::class);
        $this->application = $container->getByType(Application::class);

        $this->prepareRouter();
    }

    public function test(): void
    {
        $httpRequest = $this->createHttpRequestWithUrl('https://domain.com/hi');
        $applicationRequest = $this->router->match($httpRequest);

        ob_start();
        $this->application->processRequest($applicationRequest);
        $responseOutput = ob_get_clean();
        $this->assertSame('Hey', $responseOutput);

        $this->assertInstanceOf(StandalonePresenter::class, $this->application->getPresenter());
    }

    public function testRendering(): void
    {
        $httpRequest = $this->createHttpRequestWithUrl('https://domain.com/render-me');
        $applicationRequest = $this->router->match($httpRequest);

        ob_start();
        $this->application->processRequest($applicationRequest);
        $responseOutput = ob_get_clean();
        $this->assertSame('I was rendered!', trim($responseOutput));

        $this->assertInstanceOf(
            TemplateRendererPresenter::class,
            $this->application->getPresenter()
        );
    }

    private function createHttpRequestWithUrl(string $url): Request
    {
        return new Request(new UrlScript($url));
    }

    private function prepareRouter(): void
    {
        $this->router[] = new PresenterRoute('/hi', StandalonePresenter::class);
        $this->router[] = new PresenterRoute('/render-me', TemplateRendererPresenter::class);
    }
}
