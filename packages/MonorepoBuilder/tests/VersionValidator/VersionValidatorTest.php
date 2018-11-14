<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\MonorepoBuilder\VersionValidator;
use function Safe\sprintf;

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

        $expectedConflictingPackageVersionsPerFile = [
            sprintf('%s%sSource%2$sfirst.json', __DIR__, DIRECTORY_SEPARATOR) => '^1.0',
            sprintf('%s%sSource%2$ssecond.json', __DIR__, DIRECTORY_SEPARATOR) => '^2.0',
        ];

        $this->assertSame(
            $expectedConflictingPackageVersionsPerFile,
            $conflictingPackageVersionsPerFile['some/package']
        );
    }
}
