<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\MonorepoBuilder\VersionValidator;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;

final class VersionValidatorTest extends AbstractContainerAwareTestCase
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
        $this->versionValidator = $this->container->get(VersionValidator::class);
        $this->finderSanitizer = $this->container->get(FinderSanitizer::class);
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

        $expectedConflictingPackageVersionsPerFile = [
            __DIR__ . '/Source/first.json' => '^1.0',
            __DIR__ . '/Source/second.json' => '^2.0',
        ];

        $this->assertSame(
            $expectedConflictingPackageVersionsPerFile,
            $conflictingPackageVersionsPerFile['some/package']
        );
    }
}
