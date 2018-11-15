<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\MonorepoBuilder\VersionValidator;

final class VersionValidatorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var VersionValidator
     */
    private $versionValidator;

    protected function setUp(): void
    {
        $this->versionValidator = $this->container->get(VersionValidator::class);
    }

    public function test(): void
    {
        $fileInfos = iterator_to_array(
            Finder::create()->name('*.json')->in(__DIR__ . DIRECTORY_SEPARATOR . 'Source') ->getIterator()
        );

        $conflictingPackageVersionsPerFile = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $fileInfos
        );

        $this->assertArrayHasKey('some/package', $conflictingPackageVersionsPerFile);

        $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'Source';

        $expectedConflictingPackageVersionsPerFile = [
            $sourcePath . DIRECTORY_SEPARATOR . 'first.json' => '^1.0',
            $sourcePath . DIRECTORY_SEPARATOR . 'second.json' => '^2.0',
        ];

        $this->assertSame(
            $expectedConflictingPackageVersionsPerFile,
            $conflictingPackageVersionsPerFile['some/package']
        );
    }
}
