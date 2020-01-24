<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Yaml;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Tests\Yaml\ParameterInImportResolverSource\ImportParameterAwareYamlFileLoader;
use Symplify\PackageBuilder\Yaml\ParameterInImportResolver;

/**
 * @see ParameterInImportResolver
 */
final class ParameterInImportResolverTest extends TestCase
{
    public function test(): void
    {
        $containerBuilder = new ContainerBuilder();
        $importParameterAwareYamlFileLoader = new ImportParameterAwareYamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/CheckerTolerantYamlFileLoader')
        );

        $importParameterAwareYamlFileLoader->load($this->provideConfig());

        $this->assertTrue($containerBuilder->getParameter('it_works'));
    }

    private function provideConfig(): string
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-monorepo.yml';
        }

        return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-split.yml';
    }
}
