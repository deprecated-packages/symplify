<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Configuration\LevelConfigShortcutFinder;

final class LevelConfigShortcutFinderTest extends TestCase
{
    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/LevelConfigShortcutFinderSource/nested';

    /**
     * @var LevelConfigShortcutFinder
     */
    private $levelConfigShortcutFinder;

    protected function setUp(): void
    {
        $this->levelConfigShortcutFinder = new LevelConfigShortcutFinder();
    }

    /**
     * @dataProvider provideLevelAndConfig
     */
    public function testResolve(string $level, string $expectedConfig): void
    {
        $input = new ArrayInput([
            '--level' => $level,
        ]);

        $config = $this->levelConfigShortcutFinder->resolveLevel($input, $this->sourceDirectory);
        $this->assertSame($expectedConfig, $config);
    }

    /**
     * @return string[][]
     */
    public function provideLevelAndConfig(): array
    {
        return [
            ['someConfig', $this->sourceDirectory . '/someConfig.yml'],
            ['anotherConfig', $this->sourceDirectory . '/anotherConfig.neon'],
        ];
    }
}
