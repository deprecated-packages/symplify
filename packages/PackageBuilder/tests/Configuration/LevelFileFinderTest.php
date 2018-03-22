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
     * @dataProvider provideOptionsAndExpectedConfig()
     * @param string[] $options
     */
    public function test(array $options, string $expectedConfig): void
    {
        $config = $this->levelFileFinder->detectFromInputAndDirectory(new ArrayInput($options), $this->sourceDirectory);

        $this->assertSame($expectedConfig, $config);
    }

    public function provideOptionsAndExpectedConfig(): Iterator
    {
        yield [['-l' => 'someConfig'], $this->sourceDirectory . '/someConfig.yml'];
        yield [['--level' => 'someConfig'], $this->sourceDirectory . '/someConfig.yml'];
        yield [['--level' => 'anotherConfig'], $this->sourceDirectory . '/anotherConfig.yml'];
    }
}
