<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

final class VersionValidatorTest extends AbstractKernelTestCase
{
    /**
     * @var VersionValidator
     */
    private $versionValidator;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->versionValidator = self::$container->get(VersionValidator::class);
        $this->finderSanitizer = self::$container->get(FinderSanitizer::class);
    }

    public function test(): void
    {
        $finder = Finder::create()
            ->name('*.json')
            ->in(__DIR__ . '/Source');

        $fileInfos = $this->finderSanitizer->sanitize($finder);

        $conflictingPackageVersionsPerFile = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $fileInfos
        );

        $this->assertArrayHasKey('some/package', $conflictingPackageVersionsPerFile);

        $expectedConflictingVersionsPerFile = [
            __DIR__ . DIRECTORY_SEPARATOR . 'Source' . DIRECTORY_SEPARATOR . 'first.json' => '^1.0',
            __DIR__ . DIRECTORY_SEPARATOR . 'Source' . DIRECTORY_SEPARATOR . 'second.json' => '^2.0',
        ];

        $this->assertSame($expectedConflictingVersionsPerFile, $conflictingPackageVersionsPerFile['some/package']);
    }
}
