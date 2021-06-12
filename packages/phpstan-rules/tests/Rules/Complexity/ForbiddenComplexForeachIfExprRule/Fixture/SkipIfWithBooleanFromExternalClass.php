<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Source\ExternalCaller;

final class SkipIfWithBooleanFromExternalClass
{
    /**
     * @var ExternalCaller
     */
    private $externalCaller;

    public function __construct(ExternalCaller $externalCaller)
    {
        $this->externalCaller = $externalCaller;
    }

    public function run($value)
    {
        if ($this->externalCaller->returnsBool($value)) {
            return 100;
        }

        return 1000;
    }
}
