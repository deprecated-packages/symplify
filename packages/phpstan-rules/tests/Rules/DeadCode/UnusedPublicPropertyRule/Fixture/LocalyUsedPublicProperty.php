<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicPropertyRule\Fixture;

final class LocalyUsedPublicProperty
{
    public $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
