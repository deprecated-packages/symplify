<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\VersionValidator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Exception\AmbiguousVersionException;
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
        $fileInfos = iterator_to_array(Finder::create()
            ->name('*.json')
            ->in(__DIR__ . '/Source')
            ->getIterator());

        $this->expectException(AmbiguousVersionException::class);
        $this->versionValidator->validateFileInfos($fileInfos);
    }
}
