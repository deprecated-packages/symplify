<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\Filtering;

use PHPStan\Rules\Rule;
use Symplify\EasyCI\ActiveClass\ValueObject\FileWithClass;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;

final class PossiblyUnusedClassesFilter
{
    /**
     * @var class-string[]
     */
    private const EXCLUDED_TYPES = [ConfigurableRuleInterface::class, Rule::class];

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedNames
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedNames): array
    {
        $possiblyUnusedFilesWithClasses = [];

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedNames, true)) {
                continue;
            }

            // is excluded interfaces?
            foreach (self::EXCLUDED_TYPES as $excludedType) {
                if (is_a($fileWithClass->getClassName(), $excludedType, true)) {
                    continue 2;
                }
            }

            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }

        return $possiblyUnusedFilesWithClasses;
    }
}
