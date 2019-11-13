<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Twig;

use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\Statie\HttpKernel\StatieKernel;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

abstract class AbstractTwigExtensionTestCase extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(StatieKernel::class);
    }

    /**
     * @param mixed[] $variables
     */
    protected function renderTemplate(string $template, array $variables = []): string
    {
        $environment = self::$container->get(Environment::class);

        /** @var ArrayLoader $arrayLoader */
        $arrayLoader = self::$container->get(ArrayLoader::class);
        $arrayLoader->setTemplate($template, $template);

        return $environment->render($template, $variables);
    }
}
