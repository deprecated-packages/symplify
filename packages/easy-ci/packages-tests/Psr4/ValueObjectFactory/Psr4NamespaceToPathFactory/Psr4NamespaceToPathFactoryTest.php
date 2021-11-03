<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory;

use Iterator;
use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Psr4\Configuration\Psr4SwitcherConfiguration;
use Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPath;
use Symplify\EasyCI\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class Psr4NamespaceToPathFactoryTest extends AbstractKernelTestCase
{
    private Psr4NamespaceToPathFactory $psr4NamespaceToPathFactory;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->psr4NamespaceToPathFactory = $this->getService(Psr4NamespaceToPathFactory::class);

        /** @var Psr4SwitcherConfiguration $psr4SwitcherConfiguration */
        $psr4SwitcherConfiguration = $this->getService(Psr4SwitcherConfiguration::class);
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
