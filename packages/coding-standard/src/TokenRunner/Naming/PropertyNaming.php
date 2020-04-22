<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Naming;

use Nette\Utils\Strings;

final class PropertyNaming
{
    public function getExpectedNameFromType(string $type): string
    {
        $rawName = $type;

        // is FQN namespace
        if (Strings::contains($rawName, '\\')) {
            $rawNameParts = explode('\\', $rawName);
            $rawName = array_pop($rawNameParts);
        }

        $rawName = $this->removePrefixesAndSuffixes($rawName);

        // if all is upper-cased, it should be lower-cased
        if ($rawName === strtoupper($rawName)) {
            $rawName = strtolower($rawName);
        }

        // remove "_"
        $rawName = Strings::replace($rawName, '#_#', '');

        // turns $SOMEUppercase => $someUppercase
        for ($i = 0; $i <= strlen($rawName); ++$i) {
            if (ctype_upper($rawName[$i]) && ctype_upper($rawName[$i + 1])) {
                $rawName[$i] = strtolower($rawName[$i]);
            } else {
                break;
            }
        }

        return lcfirst($rawName);
    }

    private function removePrefixesAndSuffixes(string $rawName): string
    {
        // is SomeInterface
        if (Strings::endsWith($rawName, 'Interface')) {
            $rawName = Strings::substring($rawName, 0, -strlen('Interface'));
        }

        // is ISomeClass
        if ($this->isPrefixedInterface($rawName)) {
            $rawName = Strings::substring($rawName, 1);
        }

        // is AbstractClass
        if (Strings::startsWith($rawName, 'Abstract')) {
            $rawName = Strings::substring($rawName, strlen('Abstract'));
        }

        return $rawName;
    }

    private function isPrefixedInterface(string $rawName): bool
    {
        return strlen($rawName) > 3
            && Strings::startsWith($rawName, 'I')
            && ctype_upper($rawName[1])
            && ctype_lower($rawName[2]);
    }
}
