<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests\Filter;

use Symplify\Statie\Latte\Filter\TimeFilterProvider;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;

final class TimeFilterProviderTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var TimeFilterProvider $timeFilterProvider */
        $timeFilterProvider = $this->container->get(TimeFilterProvider::class);
        $timeToSecondsFilter = $timeFilterProvider->provide()['timeToSeconds'];

        $this->assertSame(745, $timeToSecondsFilter('12:25'));
        $this->assertSame(4345, $timeToSecondsFilter('1:12:25'));
    }
}
