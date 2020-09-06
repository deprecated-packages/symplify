<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\Process;

use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ProcessFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var ProcessFactory
     */
    private $processFactory;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->processFactory = self::$container->get(ProcessFactory::class);
    }

    public function test(): void
    {
        $subsplitProcess = $this->processFactory->createSubsplit(
            '',
            'localDirectory',
            'git@github.com:symplify/symplify.git',
            'master'
        );

        $subsplitRealpath = realpath(__DIR__ . '/../../bash/subsplit.sh');
        $commandLine = "'" . $subsplitRealpath . "' '--from-directory=localDirectory' '--to-repository=git@github.com:symplify/symplify.git' '--branch=master' %s '--repository=%s/.git'";
        $this->assertStringMatchesFormat($commandLine, $subsplitProcess->getCommandLine());
    }
}
