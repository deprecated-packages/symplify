<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\Fixture;

final class SkipValueObjectFactory
{
    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function createFromName(string $name)
    {
        return new self($name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
