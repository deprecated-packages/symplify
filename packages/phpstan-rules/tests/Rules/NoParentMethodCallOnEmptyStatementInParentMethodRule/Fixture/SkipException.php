<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

use Exception;

final class SkipException extends Exception
{
    private $value;

    public function __construct($value)
    {
        parent::__construct($value);
        $this->value = $value;
    }
}
