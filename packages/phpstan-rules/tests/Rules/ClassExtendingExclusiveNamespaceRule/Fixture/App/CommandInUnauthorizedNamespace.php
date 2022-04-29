<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App;

use Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Source\Symfony\Component\Console\Command\Command;

class CommandInUnauthorizedNamespace extends Command
{
}
