<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\StreamOutput;
use Symplify\PackageBuilder\Console\ExceptionRenderer;

final class ExceptionRendererTest extends TestCase
{
    /**
     * @var ExceptionRenderer
     */
    private $exceptionRenderer;

    /**
     * @var resource
     */
    private $tempFile;

    protected function setUp(): void
    {
        $this->exceptionRenderer = new ExceptionRenderer($this->createStreamOutput());
    }

    public function test(): void
    {
        $exception = new Exception('Random message');
        $this->exceptionRenderer->render($exception);

        $this->assertContains('In ExceptionRendererTest.php line', $this->getTestErrorOutput());
    }

    /**
     * @see http://alexandre-salome.fr/blog/Test-your-commands-in-Symfony2
     */

    private function getTestErrorOutput(): string
    {
        fseek($this->tempFile, 0);
        $output = fread($this->tempFile, 4096);
        fclose($this->tempFile);

        return $output;
    }

    /**
     * Inspired by https://stackoverflow.com/questions/18237519/symfony2-console-output-without-outputinterface
     * and http://alexandre-salome.fr/blog/Test-your-commands-in-Symfony2
     */
    private function createStreamOutput(): StreamOutput
    {
        $this->tempFile = tmpfile();
        $streamOutput = new StreamOutput($this->tempFile);

        return $streamOutput;
    }
}
