<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Naming;

final class UniqueNaming
{
    /**
     * @var array<string, int>
     */
    private array $existingNames = [];

    public function uniquateName(string $name): string
    {
        if (isset($this->existingNames[$name])) {
            $serviceNameCounter = $this->existingNames[$name];
            $this->existingNames[$name] = ++$serviceNameCounter;
            return $name . '.' . $serviceNameCounter;
        }

        $this->existingNames[$name] = 1;

        return $name;
    }
}
