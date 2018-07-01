<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Tests\Process;

use Nette\Utils\FileSystem;
use Symplify\MonorepoBuilder\Split\Process\ProcessFactory;
use Symplify\MonorepoBuilder\Split\Tests\AbstractContainerAwareTestCase;

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
        $subsplitInitProcess = $this->processFactory->createSubsplitInit();

        $subsplitInitProcess->run();

        $this->assertTrue($subsplitInitProcess->isSuccessful());
        $this->assertSame(0, $subsplitInitProcess->getExitCode());

        $this->assertDirectoryExists(getcwd() . '/.subsplit');
    }

    protected function tearDown(): void
    {
        FileSystem::delete(getcwd() . '/.subsplit');
    }
}
