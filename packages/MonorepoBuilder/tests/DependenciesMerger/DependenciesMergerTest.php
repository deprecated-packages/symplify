<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DependenciesMerger;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\DependenciesMerger;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

final class DependenciesMergerTest extends TestCase
{
    /**
     * @var DependenciesMerger
     */
    private $dependenciesMerger;

    protected function setUp(): void
    {
        $this->dependenciesMerger = new DependenciesMerger([Section::REQUIRE], new JsonFileManager());
    }

    public function test(): void
    {
        $this->dependenciesMerger->mergeJsonToRootFilePath([
            'require' => [
                'php' => '^7.1',
                'symfony/dependency-injection' => '^4.1',
            ],
        ], __DIR__ . '/Source/root.json');

        $this->assertFileEquals(__DIR__ . '/Source/expected-root.json', __DIR__ . '/Source/root.json');
    }

    protected function tearDown(): void
    {
        copy(__DIR__ . '/Source/backup-root.json', __DIR__ . '/Source/root.json');
    }
}
