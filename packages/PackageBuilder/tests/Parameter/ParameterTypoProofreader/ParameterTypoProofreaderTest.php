<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter\ParameterTypoProofreader;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Exception\Parameter\ParameterTypoException;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\HttpKernel\PackageBuilderTestKernel;

final class ParameterTypoProofreaderTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(PackageBuilderTestKernel::class, [__DIR__ . '/config.yml']);
    }

    public function testConsole(): void
    {
        $application = self::$container->get(Application::class);
        $application->setCatchExceptions(false);

        $this->expectException(ParameterTypoException::class);
        $this->expectExceptionMessage('Parameter "parameters > typo" does not exist.
Use "parameters > correct" instead.');

        $application->run(new ArrayInput(['command' => 'list']));
    }
}
