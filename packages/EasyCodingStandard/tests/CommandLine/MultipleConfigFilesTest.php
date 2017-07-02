<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\CommandLine;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class MultipleConfigFilesTest extends TestCase
{
    /**
     * @group slow
     */
    public function test(): void
    {
        $process = $this->createProcessWithConfig('MultipleConfigFilesSource/empty-config.neon');

        $this->assertTrue($process->isSuccessful());
        $this->assertSame('', $process->getErrorOutput());
        $this->assertContains('[OK] Loaded 0 checkers in total', $process->getOutput());
    }

    public function testSimpleConfig(): void
    {
        $process = $this->createProcessWithConfig('MultipleConfigFilesSource/simple-config.neon');

        $this->assertTrue($process->isSuccessful());
        $this->assertSame('', $process->getErrorOutput());
        $this->assertContains('[OK] Loaded 1 checkers in total', $process->getOutput());
    }

    private function createProcessWithConfig(string $config): Process
    {
        $process = new Process(sprintf(
            __DIR__ . '/../../bin/easy-coding-standard show --config %s',
            $config
        ), __DIR__);
        $process->run();
        return $process;
    }
}
