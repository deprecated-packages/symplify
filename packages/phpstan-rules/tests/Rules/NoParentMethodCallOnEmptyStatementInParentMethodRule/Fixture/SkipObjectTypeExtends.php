<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\Fixture;

use PHPStan\Type\ObjectType;

class SkipObjectTypeExtends extends ObjectType
{
    /**
     * @var string
     */
    private $extra;

    public function __construct(string $some, string $extra)
    {
        parent::__construct($some);
        $this->extra = $extra;
    }
}
