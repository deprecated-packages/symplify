<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Exception\Configuration\MissingGithubRepositorySlugException;

final class ConfigurationTest extends TestCase
{
    public function testSettings(): void
    {
        $container = (new ContainerFactory())->createWithConfig(__DIR__ . '/ConfigurationSource/statie-settings.neon');

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);

        $this->assertSame('blog/:year/:month/:day/:title', $configuration->getPostRoute());
        $this->assertTrue($configuration->isMarkdownHeadlineAnchors());
        $this->assertTrue($configuration->isAmpEnabled());
        $this->assertSame('TomasVotruba/tomasvotruba.cz', $configuration->getGithubRepositorySlug());
    }

    public function testMarkdownHeadlineAnchors(): void
    {
        $container = (new ContainerFactory())->create();
        $configuration = $container->get(Configuration::class);

        $configuration->enableMarkdownHeadlineAnchors();
        $this->assertTrue($configuration->isMarkdownHeadlineAnchors());

        $configuration->disableMarkdownHeadlineAnchors();
        $this->assertFalse($configuration->isMarkdownHeadlineAnchors());
    }

    public function testExceptionForEmptyGithubRepositorySlug(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/ConfigurationSource/settings-without-github-slug.neon'
        );

        $configuration = $container->get(Configuration::class);

        $this->expectException(MissingGithubRepositorySlugException::class);
        $configuration->getGithubRepositorySlug();
    }
}
