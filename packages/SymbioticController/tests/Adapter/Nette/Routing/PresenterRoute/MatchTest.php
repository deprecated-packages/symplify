<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Routing\PresenterRoute;

use Nette\Application\Request as ApplicationRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterRoute;
use Symplify\SymbioticController\Tests\Adapter\Nette\Application\PresenterFactorySource\StandalonePresenter;

final class MatchTest extends TestCase
{
    /**
     * @dataProvider matchProvider()
     *
     * @param string $route
     * @param bool $result
     * @param mixed[] $parameters
     */
    public function testMatch(string $mask, string $url, array $parameters = []): void
    {
        $httpRequest = new Request(new UrlScript($url));
        $presenterRoute = new PresenterRoute($mask, StandalonePresenter::class);

        $appRequest = $presenterRoute->match($httpRequest);
        if ($appRequest === null) {
            return;
        }

        $expectedAppRequest = new ApplicationRequest(
            StandalonePresenter::class,
            $httpRequest->getMethod(),
            $parameters,
            $httpRequest->getPost(),
            $httpRequest->getFiles(),
            [ApplicationRequest::SECURED => $httpRequest->isSecured()]
        );

        $this->assertSame($expectedAppRequest->getParameters(), $appRequest->getParameters());
        $this->assertSame($expectedAppRequest->getMethod(), $appRequest->getMethod());
        $this->assertSame($expectedAppRequest->getPresenterName(), $appRequest->getPresenterName());
    }

    /**
     * @return mixed[][]
     */
    public function matchProvider(): array
    {
        return [
            ['/hele/mese', 'http://www.hele.cz/hele/mese'],
            ['/hele/mese', 'http://www.hele.cz/hele/mese/?haha=1', ['haha' => '1']],
            ['/hele/<id>', 'http://www.hele.cz/hele/21', ['id' => '21']],
            ['/hele/<id>/mese', 'http://www.hele.cz/hele/21/mese', ['id' => '21']],
            ['/hele/<id>/<pid>', 'http://www.hele.cz/hele/123/456', [
                'id' => '123',
                'pid' => '456',
            ]],
        ];
    }

    /**
     * @dataProvider provideDataForMissedMatches()
     */
    public function testMatchShouldBeNull(string $mask, string $url): void
    {
        $httpRequest = new Request(new UrlScript($url));
        $presenterRoute = new PresenterRoute($mask, StandalonePresenter::class);

        $this->assertNull($presenterRoute->match($httpRequest));
    }

    /**
     * @return mixed[][]
     */
    public function provideDataForMissedMatches(): array
    {
        return [
            ['/hele/', 'http://www.hele.cz/hele/mese'],
            ['/hele/Mese/', 'http://www.hele.cz/hele/mese/'],
            ['/hele/<id>/mese', 'http://www.hele.cz/hele/21'],
        ];
    }
}
