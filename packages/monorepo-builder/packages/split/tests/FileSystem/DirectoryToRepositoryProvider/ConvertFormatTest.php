<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\FileSystem\DirectoryToRepositoryProvider;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Split\FileSystem\DirectoryToRepositoryProvider;
use Symplify\MonorepoBuilder\Split\ValueObject\ConvertFormat;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ConvertFormatTest extends AbstractKernelTestCase
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
        $this->parameterProvider->changeParameter(
            Option::DIRECTORIES_TO_REPOSITORIES_CONVERT_FORMAT,
            ConvertFormat::PASCAL_CASE_TO_KEBAB_CASE
        );

        $this->directoryToRepositoryProvider = self::$container->get(DirectoryToRepositoryProvider::class);
    }

    /**
     * @dataProvider provideData()
     * @param array<string, string> $parameter
     * @param array<string, string> $expectedDirectoriesToRepositories
     */
    public function test(array $parameter, array $expectedDirectoriesToRepositories): void
    {
        $this->parameterProvider->changeParameter(Option::DIRECTORIES_TO_REPOSITORIES, $parameter);

        $directoriesToRepositoies = $this->directoryToRepositoryProvider->provide();
        $this->assertSame($expectedDirectoriesToRepositories, $directoriesToRepositoies);
    }

    public function provideData(): Iterator
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/FixtureConvertFormat/PascalCasePackage');
        $relativeFilePathFromCwd = $smartFileInfo->getRelativeFilePathFromCwd();

        yield [[
            __DIR__ . '/FixtureConvertFormat/*' => 'some/*.git',
        ], [
            $relativeFilePathFromCwd => 'some/pascal-case-package.git',
        ]];
    }
}
