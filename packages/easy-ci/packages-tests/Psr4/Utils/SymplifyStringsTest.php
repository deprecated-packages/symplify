<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Psr4\Utils;

use Iterator;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Psr4\Utils\SymplifyStrings;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SymplifyStringsTest extends AbstractKernelTestCase
{
    private SymplifyStrings $symplifyStrings;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->symplifyStrings = $this->getService(SymplifyStrings::class);
    }

    /**
     * @dataProvider provideData()
     * @param string[] $values
     */
    public function test(array $values, string $expectedSharedSuffix): void
    {
        $sharedSuffix = $this->symplifyStrings->findSharedSlashedSuffix($values);
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
