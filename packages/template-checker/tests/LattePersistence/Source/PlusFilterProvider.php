<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\Tests\LattePersistence\Source;

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
