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
        $fileInfos = iterator_to_array(Finder::create()->name('*.json')->in(__DIR__ . '/Source') ->getIterator());

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
