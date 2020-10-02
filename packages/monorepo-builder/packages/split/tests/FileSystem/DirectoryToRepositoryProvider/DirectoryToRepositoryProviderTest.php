<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\FileSystem\DirectoryToRepositoryProvider;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Split\FileSystem\DirectoryToRepositoryProvider;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class DirectoryToRepositoryProviderTest extends AbstractKernelTestCase
{
    /**
     * @var DirectoryToRepositoryProvider
     */
    private $directoryToRepositoryProvider;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->parameterProvider = self::$container->get(ParameterProvider::class);
        $this->directoryToRepositoryProvider = self::$container->get(DirectoryToRepositoryProvider::class);
    }

    /**
     * @dataProvider provideData()
     * @param array<string, string> $parameter
     * @param array<string, string> $expectedDirecotiresToRepositories
     */
    public function test(array $parameter, array $expectedDirecotiresToRepositories): void
    {
        $this->parameterProvider->changeParameter(Option::DIRECTORIES_TO_REPOSITORIES, $parameter);

        $directoriesToRepositoies = $this->directoryToRepositoryProvider->provide();
        $this->assertSame($expectedDirecotiresToRepositories, $directoriesToRepositoies);
    }

    public function provideData(): Iterator
    {
        yield [[], []];
        yield [[
            __DIR__ . '/Fixture/existing-package' => 'some.git',
        ], [
            __DIR__ . '/Fixture/existing-package' => 'some.git',
        ]];
        yield [[
            __DIR__ . '/Fixture/existing-*' => 'some/*.git',
        ], [
            __DIR__ . '/Fixture/existing-package' => 'some/package.git',
        ]];
    }
}
