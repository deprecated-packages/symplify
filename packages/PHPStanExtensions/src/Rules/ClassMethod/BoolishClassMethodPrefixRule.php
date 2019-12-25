<?php declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Rules\ClassMethod;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\BooleanType;

final class BoolishClassMethodPrefixRule implements Rule
{
    /**
     * @var string[]
     */
    private const BOOL_PREFIXES = [
        'is',
        'are',
        'was',
        'will',
        'has',
        'have',
        'had',
        'do',
        'does',
        'di',
        'can',
        'could',
        'should',
        'starts',
        'ends',
        'exists',
        'supports',
        # array access
        'offsetExists',
    ];

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            throw new ShouldNotHappenException();
        }

        $methodReflection = $classReflection->getNativeMethod($node->name->name);

        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        if (! $returnType instanceof BooleanType) {
            return [];
        }

        $prefixesPattern = '#^(' . implode('|', self::BOOL_PREFIXES) . ')#';
        if (Strings::match($node->name->toString(), $prefixesPattern)) {
            return [];
        }

        $ruleError = $this->createRuleError($node, $scope);

        return [$ruleError];
    }

    private function createRuleError(ClassMethod $classMethod, Scope $scope): RuleError
    {
        $ruleErrorBuilder = RuleErrorBuilder::message(sprintf(
            'Method "%s()" returns bool type, so the name should start with is/has/was"',
            $classMethod->name->toString()
        ));

        $ruleErrorBuilder->file($scope->getFile());
        $ruleErrorBuilder->line($classMethod->getLine());

        return $ruleErrorBuilder->build();
    }
}
