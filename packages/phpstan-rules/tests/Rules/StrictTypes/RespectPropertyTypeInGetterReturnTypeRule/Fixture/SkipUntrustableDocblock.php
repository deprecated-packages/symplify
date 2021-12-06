<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class SkipUntrustableDocblock
{
    /**
     * @var float
     */
    private $value;

    public function getValue(): float|null
    {
        return $this->value;
    }
}
