<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

class WithIntersectionType
{
    /**
     * @var A&B
     */
    public $someProperty;

    public function go()
    {
        return $this->someProperty;
    }
}
