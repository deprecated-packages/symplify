<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\Twig\GithubTwigExtension;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Tests\Twig\AbstractTwigExtensionTestCase;

final class GithubTwigExtensionTest extends AbstractTwigExtensionTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            StatieKernel::class,
            [__DIR__ . '/GithubTwigExtensionSource/statie-config-with-github-slug.yaml']
        );

        $this->fileFactory = self::$container->get(FileFactory::class);

        $configuration = self::$container->get(StatieConfiguration::class);
        $configuration->setSourceDirectory(__DIR__ . '/GithubTwigExtensionSource/source');
    }

    public function test(): void
    {
        $content = $this->renderTemplate('{{ post|github_edit_post_url }}', [
            'post' => $this->getFile(),
        ]);

        $expectedUrl = 'https://github.com/TomasVotruba/tomasvotruba.cz/edit/master/source/_posts/2017-12-31-happy-new-years.md';

        $this->assertSame($expectedUrl, $content);
    }

    private function getFile(): AbstractFile
    {
        $fileInfo = new SmartFileInfo(
            __DIR__ . '/GithubTwigExtensionSource/source/_posts/2017-12-31-happy-new-years.md'
        );

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
