<?php declare(strict_types=1);

namespace Symplify\MultiCodingStandard\Contract\Application;

use Symplify\MultiCodingStandard\Application\Command\RunApplicationCommand;

interface ApplicationInterface
{
    public function runCommand(RunApplicationCommand $command) : void;
}
