<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symplify\CodingStandard\Command\CheckCommand;
use Symplify\CodingStandard\Command\FixCommand;
use Symplify\CodingStandard\Contract\Runner\RunnerCollectionInterface;
use Symplify\CodingStandard\Runner\ContribRunner;
use Symplify\CodingStandard\Runner\Psr2Runner;
use Symplify\CodingStandard\Runner\RunnerCollection;
use Symplify\CodingStandard\Runner\SymfonyRunner;
use Symplify\CodingStandard\Runner\SymplifyRunner;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Symplify Coding Standard', null);

        $runnerCollection = $this->createAndFillRunnerCollection();

        $this->add(new CheckCommand($runnerCollection));
        $this->add(new FixCommand($runnerCollection));
    }

    private function createAndFillRunnerCollection() : RunnerCollectionInterface
    {
        $runnerCollection = new RunnerCollection();
        $runnerCollection->addRunner(new SymplifyRunner());
        $runnerCollection->addRunner(new Psr2Runner());
        $runnerCollection->addRunner(new SymfonyRunner());
        $runnerCollection->addRunner(new ContribRunner());

        return $runnerCollection;
    }
}
