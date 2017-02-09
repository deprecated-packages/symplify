<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Application;

use Symplify\MultiCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\MultiCodingStandard\Contract\Application\ApplicationInterface;

final class ApplicationRunner
{
    /**
     * @var ApplicationInterface[]
     */
    private $applications = [];

    public function addApplication(ApplicationInterface $application)
    {
        $this->applications[] = $application;
    }

    public function runCommand(RunApplicationCommand $command)
    {
        foreach ($this->applications as $application) {
            $application->runCommand($command);
        }
    }
}
