<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;

final class ConfigurationTest extends TestCase
{
    public function testSettings(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/ConfigurationSource/statie-settings.yml');

        $configuration = $container->get(StatieConfiguration::class);

        $this->assertSame(
            'https://github.com/TomasVotruba/tomasvotruba.cz/tree/master/source',
            $configuration->getGithubRepositorySourceDirectory()
        );
    }

    public function testExceptionForEmptyGithubRepositorySlug(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/ConfigurationSource/settings-without-github-slug.yml'
        );

        $configuration = $container->get(StatieConfiguration::class);

        $this->expectException(MissingGithubRepositorySlugException::class);
        $configuration->getGithubRepositorySourceDirectory();
    }
}
