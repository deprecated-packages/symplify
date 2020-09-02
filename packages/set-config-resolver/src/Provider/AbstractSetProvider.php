<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Provider;

use Symplify\EasyTesting\Exception\ShouldNotHappenException;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;
use Symplify\SetConfigResolver\ValueObject\Set;

abstract class AbstractSetProvider implements SetProviderInterface
{
    /**
     * @return string[]
     */
    public function provideSetNames(): array
    {
        $setNames = [];
        foreach ($this->provide() as $set) {
            $setNames[] = $set->getName();
        }

        return $setNames;
    }

    public function provideByName(string $desiredSetName): ?Set
    {
        // 1. name-based approach
        foreach ($this->provide() as $set) {
            if ($set->getName() !== $desiredSetName) {
                continue;
            }

            return $set;
        }

        // 2. path-based approach
        foreach ($this->provide() as $set) {
            // possible bug for PHAR files, see https://bugs.php.net/bug.php?id=52769
            if (realpath($set->getSetFileInfo()->getRealPath()) !== realpath($desiredSetName)) {
                continue;
            }

            return $set;
        }

        $message = sprintf('Set "%s" was not found', $desiredSetName);
        throw new SetNotFoundException($message, $desiredSetName, $this->provideSetNames());
    }
}
