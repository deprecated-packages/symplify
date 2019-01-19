<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\TwigFactory;

use Nette\Utils\FileSystem;
use Symplify\Statie\Tests\AbstractConfigAwareContainerTestCase;
use Symplify\Statie\Twig\TwigFactory;

final class TwigFactoryTest extends AbstractConfigAwareContainerTestCase
{
    public function test(): void
    {
        $twigFactory = $this->container->get(TwigFactory::class);
        $twig = $twigFactory->create();

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
