<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App\Command;

use Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Source\Symfony\Component\Console\Command\Command;

class SkipCommandInAuthorizedNamespace extends Command
{
}
