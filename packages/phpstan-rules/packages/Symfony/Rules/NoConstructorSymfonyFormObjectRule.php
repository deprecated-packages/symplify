<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use Nette\Utils\Arrays;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Collector\ClassMethod\FormTypeClassCollector;

/**
 * @implements Rule<CollectedDataNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule\NoConstructorSymfonyFormObjectRuleTest
 */
final class NoConstructorSymfonyFormObjectRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This object is used in a Symfony form, that uses magic setters/getters, so it cannot have required constructor';

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $formTypeClassesCollector = $node->get(FormTypeClassCollector::class);
        $formTypeClasses = Arrays::flatten($formTypeClassesCollector);

        $ruleErrors = [];

        foreach ($formTypeClasses as $formTypeClass) {
            if (! $this->reflectionProvider->hasClass($formTypeClass)) {
                continue;
            }

            $formTypeClassReflection = $this->reflectionProvider->getClass($formTypeClass);

            // no constructor, we can skip
            $constructorClassReflection = $formTypeClassReflection->getConstructor();
            if (! $constructorClassReflection instanceof PhpMethodReflection) {
                continue;
            }

            if (! $this->hasClassMethodRequiredParameter($constructorClassReflection)) {
                continue;
            }

            $nativeClassReflection = $formTypeClassReflection->getNativeReflection();
            $classLine = $nativeClassReflection->getStartLine();

            $ruleErrorBuilder = RuleErrorBuilder::message(self::ERROR_MESSAGE);
            $fileName = $formTypeClassReflection->getFileName();
            if (is_string($fileName)) {
                $ruleErrorBuilder->file($fileName);
            }

            if (is_int($classLine)) {
                $ruleErrorBuilder->line($classLine);
            }

            $ruleErrors[] = $ruleErrorBuilder->build();
        }

        return $ruleErrors;
    }

    private function hasClassMethodRequiredParameter(PhpMethodReflection $phpMethodReflection): bool
    {
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($phpMethodReflection->getVariants());

        // no parameters in constructor → we can skip
        if ($parametersAcceptor->getParameters() === []) {
            return false;
        }

        foreach ($parametersAcceptor->getParameters() as $parameterReflection) {
            /** @var PhpParameterReflection $parameterReflection */
            if ($parameterReflection->isOptional()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
