<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\TwigFactory;

use Nette\Utils\FileSystem;
use Symplify\Statie\Tests\AbstractConfigAwareContainerTestCase;
use Symplify\Statie\Twig\TwigFactory;

final class TwigFactoryTest extends AbstractConfigAwareContainerTestCase
{
    /**
     * @var TwigFactory
     */
    private $twigFactory;

    protected function setUp(): void
    {
        $this->twigFactory = $this->container->get(TwigFactory::class);
    }

    public function test(): void
    {
        $twig = $this->twigFactory->create();

        $template = $twig->createTemplate(FileSystem::read(__DIR__ . '/Source/someFileToRender.twig'));

        $renderedTwig = $template->render([
            'var' => 'value',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected.html', $renderedTwig);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
