<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing;

use Nette\Application\Request as ApplicationRequest;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\Url;
use Nette\Http\UrlScript;
use OdbavTo\PresenterRoute\RouteException;
use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;

final class InvokablePresenterRouteTest extends TestCase
{
    /**
     * @var string
     */
    private const PRESENTER = 'presenter';

    /**
     * @dataProvider matchProvider
     */
    public function testMatch(string $route, string $url, bool $result, array $params = [])
    {
        $httpRequest = new Request(new UrlScript($url));
        $route = new PresenterRoute($route, self::PRESENTER, [IRequest::GET]);

        $return = $route->match($httpRequest);

        $appRequest = new ApplicationRequest(
            self::PRESENTER,
            $httpRequest->getMethod(),
            $params,
            $httpRequest->getPost(),
            $httpRequest->getFiles(),
            [ApplicationRequest::SECURED => $httpRequest->isSecured()]
        );

        $expected = $result ? $appRequest : null;
        $this->assertEquals($expected, $return);
    }

    /**
     * @return mixed[][]
     */
    public function matchProvider(): array
    {
        return [
            ['hele/mese', 'http://www.hele.cz/hele/mese', true],
            ['hele/mese/', 'http://www.hele.cz/hele/mese', true],
            ['hele/mese', 'http://www.hele.cz/hele/mese/', true],
            ['hele/mese', 'http://www.hele.cz/hele/mese/?haha=1', true, ['haha' => 1]],
            ['hele/mese/', 'http://www.hele.cz/hele/mese/', true],
            ['hele/Mese/', 'http://www.hele.cz/hele/mese/', false],
            ['hele/Mese/', 'http://www.hele.cz/hele/Mese/', true],
            ['hele/mese/', 'http://www.hele.cz/hele/Mese/', false],
            ['hele/mese', 'http://www.hele.cz/hele/', false],
            ['hele/', 'http://www.hele.cz/hele/mese', false],
            ['he-le/mese/', 'http://www.hele.cz/he-le/mese/', true],
            ['he-le/mese/', 'http://www.hele.cz/hele/mese/', false],
            ['hele/mese/', 'http://www.hele.cz/he-le/mese/', false],
            ['hele/<id>', 'http://www.hele.cz/hele/21', true, ['id' => 21]],
            ['hele/<id>/', 'http://www.hele.cz/hele/21', true, ['id' => 21]],
            ['he-le/<id>', 'http://www.hele.cz/he-le/21', true, ['id' => 21]],
            ['hele/<id>', 'http://www.hele.cz/hele/mese', true, ['id' => 'mese']],
            ['hele/<id>', 'http://www.hele.cz/hele/me3se', true, ['id' => 'me3se']],
            ['hele/<id>', 'http://www.hele.cz/hele/me-se', true, ['id' => 'me-se']],
            ['hele/<i_d>', 'http://www.hele.cz/hele/me-se', true, ['i_d' => 'me-se']],
            ['hele/<id>/mese', 'http://www.hele.cz/hele/21', false],
            ['hele/<id>/mese', 'http://www.hele.cz/hele/21/mese', true, ['id' => 21]],
            ['hele/<id>/<pid>', 'http://www.hele.cz/hele/123/456', true, ['id' => 123, 'pid' => 456]],
        ];
    }

    /**
     * @test
     * @dataProvider constructUrlProvider
     * @param string $route
     * @param string $refUrl
     */
    public function constructUrl(string $route, array $params, string $refUrl, string $expected, string $exception = '')
    {
        $appRequest = new ApplicationRequest(
            self::PRESENTER,
            IRequest::GET,
            $params
        );

        if ($exception) {
            $this->expectException($exception);
        }


        $presenterRoute = new PresenterRoute($route, self::PRESENTER, [IRequest::GET]);
        $return = $presenterRoute->constructUrl($appRequest, new Url($refUrl));

        $this->assertEquals($expected, $return);
    }

    public function constructUrlProvider()
    {
        return [
            ['hele/mese', [], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/mese'],
            ['hele/mese/', [], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/mese/'],
            ['hele/mese', [], 'http://www.hele.cz/hele', 'http://www.hele.cz/hele/mese'],
            ['hele/me-se', [], 'http://www.hele.cz/hele', 'http://www.hele.cz/hele/me-se'],
            ['hele/<id>', [], 'http://www.hele.cz/hele', 'http://www.hele.cz/hele/mese', RouteException::class],
            ['hele/<id>', ['id' => 123], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123'],
            ['hele/<i-d>', ['i-d' => 123], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123'],
            ['hele/<id>', ['id' => 'mese'], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/mese'],
            ['hele/<id>', ['id' => 'me-se'], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/me-se'],
            ['hele/<id>/', ['id' => 123], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123/'],
            ['hele/<id>/ok', ['id' => 123], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123/ok'],
            ['hele/<id>/<pid>', ['id' => 123, 'pid' => 456], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123/456'],
            ['hele/<id>/<pid>', ['id' => 123], 'http://www.hele.cz/hele/', 'http://www.hele.cz/hele/123/456', RouteException::class],
        ];
    }

    /**
     * @dataProvider restProvider
     * @param string $method
     * @param string $route
     * @param string $url
     * @param bool $result
     */
    public function testRest(?array $routeMethods, string $requestMethod, bool $result)
    {
        $restRoute = new PresenterRoute('', self::PRESENTER, $routeMethods);

        $httpRequest = new Request(new UrlScript(), null, null, null, null, null, $requestMethod);

        $returned = $restRoute->match($httpRequest);

        $this->assertEquals($result, (bool) $returned);
    }

    /**
     * @return mixed[][]
     */
    public function restProvider(): array
    {
        return [
            [[IRequest::GET, IRequest::POST], IRequest::GET, true],
            [[IRequest::GET], IRequest::POST , false],
            [[IRequest::POST], IRequest::POST , true],
            [[], IRequest::POST , false],
        ];
    }
}
