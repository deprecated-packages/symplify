<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Console;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symplify\CodingStandard\Command\CheckCommand;
use Symplify\CodingStandard\Command\FixCommand;
use Symplify\CodingStandard\Console\Application;
use Symplify\CodingStandard\Contract\Runner\RunnerCollectionInterface;

final class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    public function testDefaultCommands()
    {
        $this->assertInstanceOf(CheckCommand::class, $this->application->find('check'));
        $this->assertInstanceOf(FixCommand::class, $this->application->find('fix'));
    }

    public function testRunnerCollection()
    {
        $checkCommand = $this->application->find('check');

        /** @var RunnerCollectionInterface $runnerCollection */
        $runnerCollection = PHPUnit_Framework_Assert::getObjectAttribute($checkCommand, 'runnerCollection');

        $this->assertInstanceOf(RunnerCollectionInterface::class, $runnerCollection);
        $this->assertCount(4, $runnerCollection->getRunners());
    }
}
