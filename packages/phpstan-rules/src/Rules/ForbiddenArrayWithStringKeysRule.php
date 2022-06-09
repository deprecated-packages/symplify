<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use JsonSerializable;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ArrayType;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\PHPStanRules\Naming\AssignToVariableChecker;
use Symplify\PHPStanRules\NodeAnalyzer\ArrayAnalyzer;
use Symplify\PHPStanRules\ParentGuard\ParentElementResolver\ParentMethodReturnTypeResolver;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule\ForbiddenArrayWithStringKeysRuleTest
 *
 * @implements Rule<Return_>
 */
final class ForbiddenArrayWithStringKeysRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array with keys is not allowed. Use value object to pass data instead';

    /**
     * @var string
     * @see https://regex101.com/r/ddj4mB/2
     */
    private const TEST_FILE_REGEX = '#(Test|TestCase)\.php$#';

    /**
     * @see https://regex101.com/r/TOKYyM/1
     * @var string
     */
    private const ARRAY_CONFIGURATION_NAMES_REGEX = '#(yaml|json|neon)#i';

    public function __construct(
        private ParentMethodReturnTypeResolver $parentMethodReturnTypeResolver,
        private ArrayAnalyzer $arrayAnalyzer,
        private AssignToVariableChecker $assignToVariableChecker
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Return_::class;
    }

    /**
     * @param Return_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->expr instanceof Array_) {
            return [];
        }

        if ($this->shouldSkipClass($scope)) {
            return [];
        }

        if ($this->shouldSkipArray($node->expr, $scope)) {
            return [];
        }

        if (! $this->arrayAnalyzer->isArrayWithStringKey($node->expr)) {
            return [];
        }

        // is return array required by parent
        $parentMethodReturnType = $this->parentMethodReturnTypeResolver->resolve($scope);
        if ($parentMethodReturnType instanceof ArrayType) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        return [
            'name' => 'John',
            'surname' => 'Dope',
        ];
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        return new Person('John', 'Dope');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipArray(Array_ $array, Scope $scope): bool
    {
        if (Strings::match($scope->getFile(), self::TEST_FILE_REGEX)) {
            return true;
        }

        // skip examples in Rector::getDefinition() method
        if (in_array($scope->getFunctionName(), ['getDefinition', MethodName::CONSTRUCTOR], true)) {
            return true;
        }

        return $this->assignToVariableChecker->isAssignToVariableRegex($array, self::ARRAY_CONFIGURATION_NAMES_REGEX);
    }

    private function shouldSkipClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection) {
            if ($classReflection->isSubclassOf(JsonSerializable::class)) {
                return true;
            }

            if (str_contains($classReflection->getName(), 'json')) {
                return true;
            }

            if (str_contains($classReflection->getName(), 'Json')) {
                return true;
            }
        }

        $filePath = $scope->getFile();

        // php-scoper config, it return magic array by design
        if (\str_contains($filePath, 'scoper')) {
            return true;
        }

        // skip Symfony bundles.php
        return \str_ends_with($filePath, 'bundles.php');
    }
}
