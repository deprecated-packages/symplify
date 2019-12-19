<?php declare(strict_types=1);

namespace Symplify\AutoBindParameter\Tests\Source;

final class SomeServiceWithParameter
{
    /**
     * @var string
     */
    private $superName;

    public function __construct(string $superName)
    {
        $this->superName = $superName;
    }

    public function getSuperName(): string
    {
        return $this->superName;
    }
}
