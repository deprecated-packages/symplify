<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\DeadCode\UnusedPublicMethodSniff;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\DeadCode\UnusedPublicMethodSniff
 */
final class UnusedPublicMethodSniffTest extends AbstractCheckerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // to reset the cache inside the Sniff
        unset(parent::$cachedContainers[$this->getConfigHash()]);
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php.inc');
    }

    /**
     * @dataProvider provideWrongCases()
     */
    public function testWrong(string $file): void
    {
        $this->doTestWrongFile($file);
    }

    public function provideWrongCases(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php.inc'];
        yield [__DIR__ . '/wrong/wrong2.php.inc'];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
