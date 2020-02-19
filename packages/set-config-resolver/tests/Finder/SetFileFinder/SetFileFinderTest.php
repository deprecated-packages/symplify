<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Tests\Finder\SetFileFinder;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symplify\SetConfigResolver\Finder\SetFileFinder;

final class SetFileFinderTest extends TestCase
{
    /**
     * @var string
     */
    private const SOURCE_DIRECTORY = __DIR__ . '/SetFileFinderSource/nested';

    /**
     * @var SetFileFinder
     */
    private $setFileFinder;

    protected function setUp(): void
    {
        $this->setFileFinder = new SetFileFinder();
    }

    /**
     * @dataProvider provideOptionsAndExpectedConfig()
     * @param string[] $options
     */
    public function test(array $options, string $expectedConfig): void
    {
        $config = $this->setFileFinder->detectFromInputAndDirectory(new ArrayInput($options), self::SOURCE_DIRECTORY);

        $this->assertSame($expectedConfig, $config);
    }

    public function provideOptionsAndExpectedConfig(): Iterator
    {
        yield [['-s' => 'someConfig'], self::SOURCE_DIRECTORY . '/someConfig.yml'];

        yield [['--set' => 'someConfig'], self::SOURCE_DIRECTORY . '/someConfig.yml'];

        yield [['--set' => 'anotherConfig'], self::SOURCE_DIRECTORY . '/anotherConfig.yml'];
    }
}
