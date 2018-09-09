<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\VersionValidator;

final class VersionValidatorTest extends TestCase
{
    /**
     * @var VersionValidator
     */
    private $versionValidator;

    protected function setUp(): void
    {
        $this->versionValidator = new VersionValidator(new JsonFileManager());
    }

    public function test(): void
    {
        $fileInfos = iterator_to_array(Finder::create()->name('*.json')->in(__DIR__ . '/Source') ->getIterator());

        $conflictingPackageVersionsPerFile = $this->versionValidator->findConflictingPackageInFileInfos($fileInfos);

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
