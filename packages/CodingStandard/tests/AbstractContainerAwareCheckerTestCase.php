<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\AutowireCheckersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\GenericContainerFactory;
use Symplify\PackageBuilder\FileSystem\FileGuard;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

abstract class AbstractContainerAwareCheckerTestCase extends AbstractSimpleFixerTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp(): void
    {
        $this->container = (new GenericContainerFactory())->createWithConfigsAndCompilerPasses(
            $this->prepareConfigs(),
            $this->prepareCompilerPasses()
        );

        parent::setUp();
    }

    abstract protected function provideConfig(): string;

    /**
     * @return string[]
     */
    private function prepareConfigs(): array
    {
        $configs = [];
        $configs[] = __DIR__ . '/config/services.yml';

        FileGuard::ensureFileExists($this->provideConfig(), get_called_class());
        $configs[] = $this->provideConfig();

        return $configs;
    }

    /**
     * @return CompilerPassInterface[]
     */
    private function prepareCompilerPasses(): array
    {
        return [
            new AutowireCheckersCompilerPass(),
        ];
    }
}
