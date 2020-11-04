<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator;

use ReflectionClass;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class RuleDefinitionsResolver
{
    /**
     * @param string[] $classNames
     * @return RuleDefinition[]
     */
    public function resolveFromClassNames(array $classNames): array
    {
        $ruleDefinitions = [];

        foreach ($classNames as $className) {
            $reflectionClass = new ReflectionClass($className);
            $documentedRule = $reflectionClass->newInstanceWithoutConstructor();
            if (! $documentedRule instanceof DocumentedRuleInterface) {
                throw new ShouldNotHappenException();
            }

            $ruleDefinition = $documentedRule->getRuleDefinition();
            $ruleDefinition->setRuleClass($className);
            $ruleDefinitions[] = $ruleDefinition;
        }

        return $ruleDefinitions;
    }
}
