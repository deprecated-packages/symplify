<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Templating\FilterProvider;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Templating\FilterProvider\TimeFilterProvider;

final class TimeFilterProviderTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(StatieKernel::class);

        $timeFilterProvider = self::$container->get(TimeFilterProvider::class);
        $timeToSecondsFilter = $timeFilterProvider->provide()['timeToSeconds'];

        $this->assertSame(745, $timeToSecondsFilter('12:25'));
        $this->assertSame(4345, $timeToSecondsFilter('1:12:25'));
    }
}
