<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App\Services;

use Exception;

class SkipExceptionInAuthorizedNamespace extends Exception
{
}
