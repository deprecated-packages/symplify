<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests\TwigFactory;

use Nette\Utils\FileSystem;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\HttpKernel\StatieKernel;
use Symplify\Statie\Twig\TwigFactory;

final class TwigFactoryTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernelWithConfigs(StatieKernel::class, [__DIR__ . '/config.yml']);

        $twigFactory = self::$container->get(TwigFactory::class);
        $twig = $twigFactory->create();

        $template = $twig->createTemplate(FileSystem::read(__DIR__ . '/Source/someFileToRender.twig'));

        $renderedTwig = $template->render([
            'var' => 'value',
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected.html', $renderedTwig);
    }
}
