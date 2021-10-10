<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

final class PossiblyUnusedClassesFilter
{
    /**
     * @var class-string[]
     */
    private const EXCLUDED_TYPES = [ConfigurableRuleInterface::class, Rule::class];

    /**
     * @param string[] $checkClassNames
     * @param string[] $allClassUses
     * @return string[]
     */
    public function filter(array $checkClassNames, array $allClassUses): array
    {
        $possiblyUnusedClasses = [];

        foreach ($checkClassNames as $checkClassName) {
            if (in_array($checkClassName, $allClassUses, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach (self::EXCLUDED_TYPES as $excludedType) {
                if (is_a($checkClassName, $excludedType, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedClasses[] = $checkClassName;
        }

        return $possiblyUnusedClasses;
    }
}
