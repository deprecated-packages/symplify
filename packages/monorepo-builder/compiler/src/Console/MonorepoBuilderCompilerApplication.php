<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Compiler\Console;

use Symfony\Component\Console\Application;
use Symplify\MonorepoBuilder\Compiler\Command\CompileCommand;

final class MonorepoBuilderCompilerApplication extends Application
{
    public function __construct(CompileCommand $compileCommand)
    {
        parent::__construct('monorepo-builder.phar Compiler', 'v1.0');

        $this->add($compileCommand);
        $this->setDefaultCommand(CompileCommand::NAME, true);
    }
}
