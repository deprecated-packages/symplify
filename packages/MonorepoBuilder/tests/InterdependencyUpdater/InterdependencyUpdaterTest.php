<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\InterdependencyUpdater;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\InterdependencyUpdater;

final class InterdependencyUpdaterTest extends TestCase
{
    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    protected function setUp(): void
    {
        $this->interdependencyUpdater = new InterdependencyUpdater(new JsonFileManager());
    }

    protected function tearDown(): void
    {
        copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    public function test(): void
    {
        $this->interdependencyUpdater->updateFileInfosWithVendorAndVersion(
            [new SplFileInfo(__DIR__ . '/Source/first.json', 'Source/first.json', 'Source')],
            'symplify',
            '^4.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first.json', __DIR__ . '/Source/first.json');
    }
}
