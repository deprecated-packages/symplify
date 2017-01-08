<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\ServiceClass;

use PHPUnit\Framework\TestCase;
use Symplify\AutoServiceRegistration\ServiceClass\ServiceClassFinder;
use Symplify\AutoServiceRegistration\Tests\ServiceClass\ServiceFinderSource\SomeController;
use Symplify\AutoServiceRegistration\Tests\ServiceClass\ServiceFinderSource\SomeRepository;

final class ServiceFinderTest extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(array $dirs, array $classSuffixes, array $foundClasses)
    {
        $serviceFinder = new ServiceClassFinder();
        $classes = $serviceFinder->findServicesInDirsByClassSuffix($dirs, $classSuffixes);
        $this->assertSame($foundClasses, $classes);
    }

    public function provideData() : array
    {
        return [
            [[__DIR__ . '/ServiceFinderSource'], ['Controller'], [SomeController::class]],
            [[__DIR__ . '/ServiceFinderSource'], ['Repository'], [SomeRepository::class]],
        ];
    }
}
