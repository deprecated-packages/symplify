<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App\Exception;

use Exception;

class SkipExceptionInAuthorizedNamespace extends Exception
{
}
