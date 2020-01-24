<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

final class SomeFixer2
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $service;

    /**
     * @var int
     */
    private $price;
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

final class SomeFixer2
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $price;

    /**
     * @var Type
     */
    private $service;
}

?>
