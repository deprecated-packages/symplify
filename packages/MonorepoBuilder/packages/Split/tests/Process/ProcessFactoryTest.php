<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\Process;

use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\MonorepoBuilder\Split\Tests\AbstractContainerAwareTestCase;
use function Safe\realpath;

final class ProcessFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ProcessFactory
     */
    private $processFactory;

    protected function setUp(): void
    {
        $this->processFactory = $this->container->get(ProcessFactory::class);
    }

    public function test(): void
    {
        $subsplitProcess = $this->processFactory->createSubsplit(
            '',
            'localDirectory',
            'git@github.com:Symplify/Symplify.git'
        );

        $subsplitRealpath = realpath(__DIR__ . '/../../bash/subsplit.sh');
        $commandLine = "'" . $subsplitRealpath . "' '--from-directory=localDirectory' '--to-repository=git@github.com:Symplify/Symplify.git' '--branch=master' '' '--repository=%s/.git'";

        $this->assertStringMatchesFormat($commandLine, $subsplitProcess->getCommandLine());
    }
}
