<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console;

use Error;
use Exception;
use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symplify\PackageBuilder\Console\ThrowableRenderer;

final class ThrowableRendererTest extends TestCase
{
    /**
     * @var resource
     */
    private $tempFile;

    /**
     * @see https://phpunit.readthedocs.io/en/latest/assertions.html#assertstringmatchesformat
     */
    public function test(): void
    {
        $throwableRenderer = new ThrowableRenderer($this->createStreamOutput());
        $throwableRenderer->render(new Exception('Random message'));

        $this->assertStringMatchesFormat(
            '%wIn ThrowableRendererTest.php line %d:%wRandom message%w',
            $this->getTestErrorOutput()
        );
    }

    public function testNestedException(): void
    {
        $throwableRenderer = new ThrowableRenderer($this->createStreamOutput());
        $throwableRenderer->render(new Exception('Random message', 404, new Exception('Parent message')));

        $this->assertStringMatchesFormat(
            '%wIn ThrowableRendererTest.php line %d:%wRandom message%w' .
            'In ThrowableRendererTest.php line %d:%wParent message%w',
            $this->getTestErrorOutput()
        );
    }

    /**
     * @dataProvider provideVerbosityLevelsThrowableClassAndExpectedMessage()
     */
    public function testAll(?string $verbosityOption, string $throwableClass, string $expectedOutput): void
    {
        $arrayInput = new ArrayInput($verbosityOption ? [$verbosityOption => true] : []);

        $throwableRenderer = new ThrowableRenderer($this->createStreamOutput(), $arrayInput);
        $throwableRenderer->render(new $throwableClass('Random message'));

        $this->assertStringMatchesFormat($expectedOutput, $this->getTestErrorOutput());
    }

    public function provideVerbosityLevelsThrowableClassAndExpectedMessage(): Iterator
    {
        yield [null, Error::class, '%wIn ThrowableRendererTest.php line %d:%wRandom message%w'];
        yield ['-v', Error::class, '%wIn ThrowableRendererTest.php line %d:%w[ErrorException]%wRandom message%wException trace:%a'];
        yield ['-vv', Error::class, '%wIn ThrowableRendererTest.php line %d:%w[ErrorException]%wRandom message%wException trace:%a'];
        yield ['-vvv', Error::class, '%wIn ThrowableRendererTest.php line %d:%w[ErrorException]%wRandom message%wException trace:%a'];
    }

    /**
     * Inspired by http://alexandre-salome.fr/blog/Test-your-commands-in-Symfony2
     */
    private function getTestErrorOutput(): string
    {
        fseek($this->tempFile, 0);
        $output = fread($this->tempFile, 4096);
        fclose($this->tempFile);

        return $output;
    }

    private function createStreamOutput(): StreamOutput
    {
        $this->tempFile = tmpfile();
        $streamOutput = new StreamOutput($this->tempFile);

        return $streamOutput;
    }
}
