<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Latte\LattePersistence\Source;

use Symplify\EasyCI\Tests\LattePersistence\Source\SomeStaticClass;

final class PlusFilterProvider
{
    /**
     * @var string
     */
    public const NAME = 'plus';

    public function __invoke(int $number, int $anotherNumber): int
    {
        return SomeStaticClass::plus($number, $anotherNumber);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
