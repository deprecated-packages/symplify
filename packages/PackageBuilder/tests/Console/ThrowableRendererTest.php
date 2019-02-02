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
     * @dataProvider provideVerbosityLevelsThrowableClassAndExpectedMessage()
     */
    public function test(?string $verbosityOption, string $throwableClass, string $expectedOutput): void
    {
        $arrayInput = new ArrayInput($verbosityOption ? [$verbosityOption => true] : []);

        $throwableRenderer = new ThrowableRenderer($this->createStreamOutput(), $arrayInput);
        $throwableRenderer->render(new $throwableClass('Random message'));

        /** @see https://phpunit.readthedocs.io/en/latest/assertions.html#assertstringmatchesformat */
        $this->assertStringMatchesFormat($expectedOutput, $this->getTestErrorOutput());
    }

    public function provideVerbosityLevelsThrowableClassAndExpectedMessage(): Iterator
    {
        yield [null, Error::class, '%wIn ThrowableRendererTest.php line %d:%wRandom message%w'];

        $verboseErrorMessage = '%wIn ThrowableRendererTest.php line %d:%w' .
            '[Symfony\Component\Debug\Exception\FatalThrowableError]%wRandom message%wException trace:%a';
        yield ['-v', Error::class, $verboseErrorMessage];
        yield ['-vv', Error::class, $verboseErrorMessage];
        yield ['-vvv', Error::class, $verboseErrorMessage];

        yield [null, Exception::class, '%wIn ThrowableRendererTest.php line %d:%wRandom message%w'];
        yield [
            '-vvv',
            Exception::class,
            '%wIn ThrowableRendererTest.php line %d:%w[Exception]%wRandom message%wException trace:%a',
        ];
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

    private function createStreamOutput(): StreamOutput
    {
        $this->tempFile = tmpfile();
        return new StreamOutput($this->tempFile);
    }

    /**
     * Inspired by http://alexandre-salome.fr/blog/Test-your-commands-in-Symfony2
     */
    private function getTestErrorOutput(): string
    {
        fseek($this->tempFile, 0);
        $output = fread($this->tempFile, 4096);
        fclose($this->tempFile);

        return (string) $output;
    }
}
