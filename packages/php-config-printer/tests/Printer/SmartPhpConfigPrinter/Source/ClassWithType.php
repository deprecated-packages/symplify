<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source;

use PHPStan\Type\Type;

final class ClassWithType
{
    /**
     * @var Type
     */
    private $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
