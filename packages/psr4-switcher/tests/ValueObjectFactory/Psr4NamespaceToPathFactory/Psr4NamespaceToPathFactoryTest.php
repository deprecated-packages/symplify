<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Tests\ValueObjectFactory\Psr4NamespaceToPathFactory;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\Psr4Switcher\Configuration\Psr4SwitcherConfiguration;
use Symplify\Psr4Switcher\HttpKernel\Psr4SwitcherKernel;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPath;
use Symplify\Psr4Switcher\ValueObjectFactory\Psr4NamespaceToPathFactory;

final class Psr4NamespaceToPathFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var Psr4NamespaceToPathFactory
     */
    private $psr4NamespaceToPathFactory;

    protected function setUp(): void
    {
        $this->bootKernel(Psr4SwitcherKernel::class);
        $this->psr4NamespaceToPathFactory = self::$container->get(Psr4NamespaceToPathFactory::class);

        /** @var Psr4SwitcherConfiguration $psr4SwitcherConfiguration */
        $psr4SwitcherConfiguration = self::$container->get(Psr4SwitcherConfiguration::class);
        $psr4SwitcherConfiguration->loadForTest(__DIR__ . '/Source/some_composer.json');
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $class, string $file, string $expectedNamespace, string $expectedPath): void
    {
        $psr4NamespaceToPath = $this->psr4NamespaceToPathFactory->createFromClassAndFile($class, $file);
        $this->assertInstanceOf(Psr4NamespaceToPath::class, $psr4NamespaceToPath);

        /** @var Psr4NamespaceToPath $psr4NamespaceToPath */
        $this->assertSame($expectedNamespace, $psr4NamespaceToPath->getNamespace());
        $this->assertSame($expectedPath, $psr4NamespaceToPath->getPath());
    }

    public function provideData(): Iterator
    {
        yield [
            'App\Utils\CustomMacros', '../project/project-nested/libs/My/CustomMacros.php',
            // expected
            'App\Utils', '../project/project-nested/libs/My',
        ];

        yield [
            'App\Utils\CustomMacros', '../project/project-nested/libs/App/Utils/CustomMacros.php',
            // expected
            'App', '../project/project-nested/libs/App',
        ];
    }
}
