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
     * @param string $url
     * @param bool $result
     * @param mixed[] $parameters
     */
    public function testMatch(string $mask, string $url, array $parameters = []): void
    {
        $httpRequest = new Request(new UrlScript($url));
        $presenterRoute = new PresenterRoute($mask, StandalonePresenter::class);

        $appRequest = $presenterRoute->match($httpRequest);

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
            ['hele/mese', 'http://www.hele.cz/hele/mese'],
            ['hele/mese/', 'http://www.hele.cz/hele/mese'],
            ['hele/mese', 'http://www.hele.cz/hele/mese/'],
            ['hele/mese', 'http://www.hele.cz/hele/mese/?haha=1', ['haha' => 1]],
            ['hele/mese/', 'http://www.hele.cz/hele/mese/'],
            ['hele/Mese/', 'http://www.hele.cz/hele/Mese/'],
            ['he-le/mese/', 'http://www.hele.cz/he-le/mese/'],
            ['hele/<id>', 'http://www.hele.cz/hele/21', ['id' => 21]],
            ['hele/<id>/', 'http://www.hele.cz/hele/21', ['id' => 21]],
            ['he-le/<id>', 'http://www.hele.cz/he-le/21', ['id' => 21]],
            ['hele/<id>', 'http://www.hele.cz/hele/mese', ['id' => 'mese']],
            ['hele/<id>', 'http://www.hele.cz/hele/me3se', ['id' => 'me3se']],
            ['hele/<id>', 'http://www.hele.cz/hele/me-se', ['id' => 'me-se']],
            ['hele/<i_d>', 'http://www.hele.cz/hele/me-se', ['i_d' => 'me-se']],
            ['hele/<id>/mese', 'http://www.hele.cz/hele/21/mese', ['id' => 21]],
            ['hele/<id>/<pid>', 'http://www.hele.cz/hele/123/456', ['id' => 123, 'pid' => 456]],
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
            ['hele/Mese/', 'http://www.hele.cz/hele/mese/'],
            ['hele/mese/', 'http://www.hele.cz/hele/Mese/'],
            ['hele/mese', 'http://www.hele.cz/hele/'],
            ['hele/', 'http://www.hele.cz/hele/mese'],
            ['he-le/mese/', 'http://www.hele.cz/hele/mese/'],
            ['hele/mese/', 'http://www.hele.cz/he-le/mese/'],
            ['hele/<id>/mese', 'http://www.hele.cz/hele/21'],
        ];
    }
}
