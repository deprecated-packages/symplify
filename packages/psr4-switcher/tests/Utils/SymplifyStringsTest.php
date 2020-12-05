<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Tests\Utils;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\Psr4Switcher\HttpKernel\Psr4SwitcherKernel;
use Symplify\Psr4Switcher\Utils\SymplifyStrings;

final class SymplifyStringsTest extends AbstractKernelTestCase
{
    /**
     * @var SymplifyStrings
     */
    private $symplifyStrings;

    protected function setUp(): void
    {
        $this->bootKernel(Psr4SwitcherKernel::class);
        $this->symplifyStrings = $this->getService(SymplifyStrings::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $strings, string $expectedSharedSuffix): void
    {
        $sharedSuffix = $this->symplifyStrings->findSharedSlashedSuffix($strings);
        $this->assertSame($expectedSharedSuffix, $sharedSuffix);
    }

    public function provideData(): Iterator
    {
        yield [['Car', 'BusCar'], 'Car'];
        yield [['Apple\Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Apple/Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Components\ChatFriends', 'ChatFriends\ChatFriends'], 'ChatFriends'];
    }
}
