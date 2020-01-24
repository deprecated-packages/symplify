<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;
use Symplify\Statie\HttpKernel\StatieKernel;

final class ConfigurationTest extends AbstractKernelTestCase
{
    public function testSettings(): void
    {
        $this->bootKernelWithConfigs(StatieKernel::class, [__DIR__ . '/ConfigurationSource/statie-settings.yml']);

        $configuration = self::$container->get(StatieConfiguration::class);

        $this->assertSame(
            'https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/source',
            $configuration->getGithubRepositorySourceDirectory()
        );
    }

    public function testExceptionForEmptyGithubRepositorySlug(): void
    {
        $this->bootKernelWithConfigs(
            StatieKernel::class,
            [__DIR__ . '/ConfigurationSource/settings-without-github-slug.yml']
        );

        $configuration = self::$container->get(StatieConfiguration::class);

        $this->expectException(MissingGithubRepositorySlugException::class);
        $configuration->getGithubRepositorySourceDirectory();
    }
}
