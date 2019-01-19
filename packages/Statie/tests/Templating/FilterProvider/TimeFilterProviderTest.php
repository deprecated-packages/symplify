<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Templating\FilterProvider;

use Symplify\Statie\Templating\FilterProvider\TimeFilterProvider;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class TimeFilterProviderTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $timeFilterProvider = $this->container->get(TimeFilterProvider::class);
        $timeToSecondsFilter = $timeFilterProvider->provide()['timeToSeconds'];

        $this->assertSame(745, $timeToSecondsFilter('12:25'));
        $this->assertSame(4345, $timeToSecondsFilter('1:12:25'));
    }
}
