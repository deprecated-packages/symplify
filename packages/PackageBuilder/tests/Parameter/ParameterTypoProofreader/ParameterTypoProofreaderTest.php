<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter\ParameterTypoProofreader;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Exception\Parameter\ParameterTypoException;
use Symplify\PackageBuilder\Tests\ContainerFactory;

final class ParameterTypoProofreaderTest extends TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function setUp(): void
    {
        $this->container = (new ContainerFactory())->createWithConfig(__DIR__ . '/config.yml');
    }

    public function testConsole(): void
    {
        /** @var Application $application */
        $application = $this->container->get(Application::class);
        $application->setCatchExceptions(false);

        $this->expectException(ParameterTypoException::class);
        $this->expectExceptionMessage('Parameter "parameters > typo" does not exist.
Use "parameters > correct" instead.');

        $application->run(new ArrayInput(['command' => 'list']));
    }
}
