<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Configuration;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\PackageBuilder\Configuration\LevelFileFinder;

final class LevelFileFinderTest extends TestCase
{
    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/LevelFileFinderSource/nested';

    /**
     * @var LevelFileFinder
     */
    private $levelFileFinder;

    protected function setUp(): void
    {
        $this->levelFileFinder = new LevelFileFinder();
    }

    /**
     * @dataProvider provideLevelAndConfig()
     * @param string[] $options
     */
    public function testResolve(array $options, string $expectedConfig): void
    {
        $input = new ArrayInput($options);

        $config = $this->levelFileFinder->resolveLevel($input, $this->sourceDirectory);

        $this->assertSame($expectedConfig, $config);
    }

    public function provideLevelAndConfig(): Iterator
    {
        yield [['-l' => 'someConfig'], $this->sourceDirectory . '/someConfig.yml'];
        yield [['--level' => 'someConfig'], $this->sourceDirectory . '/someConfig.yml'];
        yield [['--level' => 'anotherConfig'], $this->sourceDirectory . '/anotherConfig.yml'];
    }
}
