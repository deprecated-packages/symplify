<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Configuration;

use Iterator;
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
     * @dataProvider provideLevelAndConfig()
     * @param string[] $options
     */
    public function testResolve(array $options, string $expectedConfig): void
    {
        $input = new ArrayInput($options);

        $config = $this->levelConfigShortcutFinder->resolveLevel($input, $this->sourceDirectory);

        $this->assertSame($expectedConfig, $config);
    }

    public function provideLevelAndConfig(): Iterator
    {
        yield [['--level' => 'someConfig'], $this->sourceDirectory . '/someConfig.yml'];
        yield [['--level' => 'anotherConfig'], $this->sourceDirectory . '/anotherConfig.yml'];
    }
}
