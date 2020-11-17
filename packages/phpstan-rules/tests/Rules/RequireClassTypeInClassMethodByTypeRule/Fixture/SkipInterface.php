<?php


namespace Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireClassTypeInClassMethodByTypeRule\Source\AnyParentGetTypesInterface;

interface SkipInterface extends AnyParentGetTypesInterface
{
    /**
     * @return string[]
     */
    public function getNodeTypes(): array;
}
