<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Latte\Filter\GithubPrLinkFilterProvider;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\FileFactory;

final class GithubPrLinkFilterProviderTest extends TestCase
{
    /**
     * @var Container|ContainerInterface
     */
    private $container;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/GithubPrLinkFilterProviderSource/statie-config-with-github-slug.yml'
        );

        $this->fileFactory = $this->container->get(FileFactory::class);

        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->setSourceDirectory(__DIR__ . '/GithubPrLinkFilterProviderSource/source');
    }

    public function test(): void
    {
        /** @var GithubPrLinkFilterProvider $githubPrLinkFilterProvider */
        $githubPrLinkFilterProvider = $this->container->get(GithubPrLinkFilterProvider::class);
        $githubEditPostUrlFilter = $githubPrLinkFilterProvider->provide()['githubEditPostUrl'];

        $this->assertSame(
            'https://github.com/TomasVotruba/tomasvotruba.cz/edit/master/source/_posts/2017-12-31-happy-new-years.md',
            $githubEditPostUrlFilter($this->getFile())
        );
    }

    private function getFile(): AbstractFile
    {
        $finder = Finder::create()
            ->files()
            ->in(__DIR__ . '/GithubPrLinkFilterProviderSource/source');

        $fileInfos = iterator_to_array($finder->getIterator());
        $fileInfo = array_pop($fileInfos);

        return $this->fileFactory->createFromFileInfo($fileInfo);
    }
}
