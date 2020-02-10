<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\Yaml;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\Autodiscovery\ValueObject\ServiceConfig;
use Symplify\Autodiscovery\Yaml\CommonNamespaceResolver;

final class CommonNamespaceResolverTest extends TestCase
{
    /**
     * @var CommonNamespaceResolver
     */
    private $commonNamespaceResolver;

    protected function setUp(): void
    {
        $this->commonNamespaceResolver = new CommonNamespaceResolver();
    }

    /**
     * @param string[] $classes
     * @param string[] $expectedNamespaces
     * @dataProvider provideData()
     */
    public function test(array $classes, int $level, array $expectedNamespaces): void
    {
        $serviceConfig = new ServiceConfig($classes);

        $this->assertSame($expectedNamespaces, $this->commonNamespaceResolver->resolve($serviceConfig, $level));
    }

    public function provideData(): Iterator
    {
        yield [['App\FirstClass', 'App\AnotherClass'], 1, ['App']];
        yield [['App\Wohoo\FirstClass', 'App\Wohoo\AnotherClass'], 2, ['App\Wohoo']];
        yield [['App\Wohoo\FirstClass', 'App\Wohoo\AnotherClass'], 1, ['App']];
        yield [
            [
                'Shopsys\FrameworkBundle\Model\Payment\PaymentRepository',
                'Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade',
                'Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig',
            ],
            2, ['Shopsys\FrameworkBundle'],
        ];
        yield [
            [
                'Shopsys\FrameworkBundle\Model\Payment\PaymentRepository',
                'Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade',
                'Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig',
            ],
            3, ['Shopsys\FrameworkBundle\Model', 'Shopsys\FrameworkBundle\Component'],
        ];
    }
}
