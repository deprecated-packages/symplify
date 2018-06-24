<?php declare(strict_types=1);

namespace Symplify\Statie\Twig\Tests;

use Symplify\Statie\Tests\AbstractConfigAwareContainerTestCase;
use Symplify\Statie\Tests\AbstractContainerAwareTestCase;
use Symplify\Statie\Twig\TwigFactory;

final class TwigFactoryTest extends AbstractConfigAwareContainerTestCase
{
    /**
     * @var TwigFactory
     */
    private $twigFactory;

    protected function setUp()
    {
        $this->twigFactory = $this->container->get(TwigFactory::class);
    }

    public function test()
    {
        $twig = $this->twigFactory->create();

        $template = $twig->createTemplate(file_get_contents(__DIR__ . '/TwigFactorySource/someFileToRender.twig'));

        $renderedTwig = $template->render([
            'var' => 'value'
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/TwigFactorySource/expected.html', $renderedTwig);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/TwigFactorySource/config.yml';
    }
}
