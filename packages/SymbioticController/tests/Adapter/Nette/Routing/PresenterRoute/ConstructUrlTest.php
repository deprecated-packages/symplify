<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing\PresenterRoute;

use Nette\Application\Request as ApplicationRequest;
use Nette\Http\IRequest;
use Nette\Http\Url;
use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;

final class ConstructUrlTest extends TestCase
{
    /**
     * @dataProvider constructUrlProvider()
     *
     * @param string $route
     * @param mixed[] $parameters
     * @param string $baseUrl
     * @param string $expected
     */
    public function testConstructUrl(string $route, array $parameters, string $expected): void
    {
        $appRequest = $this->createApplicationRequestWithParameters($parameters);

        $presenterRoute = new PresenterRoute($route, StandalonePresenter::class);
        $return = $presenterRoute->constructUrl($appRequest, new Url(''));

        $this->assertSame($expected, $return);
    }

    /**
     * @return mixed[][]
     */
    public function constructUrlProvider(): array
    {
        return [
            ['mese', [], '/mese'],
            ['me-se', [], '/me-se'],
            ['mese/', [], '/mese/'],
            ['<id>', ['id' => 123], '/123'],
            ['<i-d>', ['i-d' => 123], '/123'],
            ['<id>', ['id' => 'mese'], '/mese'],
            ['<id>/ok', ['id' => 123], '/123/ok'],
            ['<id>/<pid>', ['id' => 123, 'pid' => 456], '/123/456']
        ];
    }

    /**
     * @expectedException \OdbavTo\PresenterRoute\RouteException
     *
     * @dataProvider failingConstructUrlProvider()
     *
     * @param string $route
     * @param mixed[] $parameters
     */
    public function testConstructUrlFails(string $route, array $parameters): void
    {
        $appRequest = $this->createApplicationRequestWithParameters($parameters);

        $presenterRoute = new PresenterRoute($route, StandalonePresenter::class);
        $presenterRoute->constructUrl($appRequest, new Url(''));
    }

    /**
     * @return mixed[][]
     */
    public function failingConstructUrlProvider(): array
    {
        return [
            ['/<id>', []],
            ['/<id>/<pid>', ['id' => 123]]
        ];
    }

    /**
     * @param mixed[] $parameters
     */
    private function createApplicationRequestWithParameters(array $parameters): ApplicationRequest
    {
        return new ApplicationRequest(StandalonePresenter::class, IRequest::GET, $parameters);
    }
}
