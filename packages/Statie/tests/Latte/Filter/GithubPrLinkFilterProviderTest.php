<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Latte\Filter;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use SplFileInfo;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\DependencyInjection\ContainerFactory;
use Symplify\Statie\Generator\Configuration\GeneratorElement;
use Symplify\Statie\Generator\ObjectFactory;
use Symplify\Statie\Latte\Filter\GithubPrLinkFilterProvider;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\File;

final class GithubPrLinkFilterProviderTest extends TestCase
{
    /**
     * @var Container|ContainerInterface
     */
    private $container;

    /**
     * @var ObjectFactory
     */
    private $objectFactory;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/GithubPrLinkFilterProviderSource/statie-config-with-github-slug.yml'
        );

        $this->objectFactory = $this->container->get(ObjectFactory::class);

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
        $fileInfo = new SplFileInfo(
            __DIR__ . '/GithubPrLinkFilterProviderSource/source/_posts/2017-12-31-happy-new-years.md'
        );

        $dummyPostElement = GeneratorElement::createFromConfiguration([
            'variable' => '...',
            'variable_global' => '...',
            'path' => '...',
            'layout' => '...',
            'route_prefix' => '...',
            'object' => File::class
        ]);

        $objectFile = $this->objectFactory->createFromFileInfosAndGeneratorElement(
            [$fileInfo], $dummyPostElement
        );

        return $objectFile[0];
    }
}
