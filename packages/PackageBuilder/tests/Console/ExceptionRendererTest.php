<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symplify\PackageBuilder\Console\ExceptionRenderer;

final class ExceptionRendererTest extends TestCase
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
        $exceptionRenderer = new ExceptionRenderer($this->createStreamOutput());
        $exception = new Exception('Random message');
        $exceptionRenderer->render($exception);

        $this->assertStringMatchesFormat(
            '%wIn ExceptionRendererTest.php line %d:%wRandom message%w',
            $this->getTestErrorOutput()
        );
    }

    public function testNestedException(): void
    {
        $exceptionRenderer = new ExceptionRenderer($this->createStreamOutput());
        $exception = new Exception('Random message', 404, new Exception('Parent message'));
        $exceptionRenderer->render($exception);

        $this->assertStringMatchesFormat(
            '%wIn ExceptionRendererTest.php line %d:%wRandom message%w' .
            'In ExceptionRendererTest.php line %d:%wParent message%w',
            $this->getTestErrorOutput()
        );
    }

    public function testExceptionWithVerbosity(): void
    {
        $arrayInput = new ArrayInput(['v' => true]);
        $exceptionRenderer = new ExceptionRenderer($this->createStreamOutput(), $arrayInput);

        $exception = new Exception('Random message');
        $exceptionRenderer->render($exception);

        $this->assertStringMatchesFormat(
            '%wIn ExceptionRendererTest.php line %d:%w[Exception]%wRandom message%wException trace:%a',
            $this->getTestErrorOutput()
        );
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
