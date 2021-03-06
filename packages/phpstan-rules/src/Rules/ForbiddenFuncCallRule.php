<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use SimpleXMLElement;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\ForbiddenFuncCallRuleTest
 */
final class ForbiddenFuncCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Function "%s()" cannot be used/left in the code';

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var string[]
     */
    private $forbiddenFunctions = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ObjectTypeAnalyzer
     */
    private $objectTypeAnalyzer;

    /**
     * @param string[] $forbiddenFunctions
     */
    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        SimpleNameResolver $simpleNameResolver,
        ObjectTypeAnalyzer $objectTypeAnalyzer,
        array $forbiddenFunctions
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->forbiddenFunctions = $forbiddenFunctions;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->objectTypeAnalyzer = $objectTypeAnalyzer;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $funcName = $this->simpleNameResolver->getName($node);
        if ($funcName === null) {
            return [];
        }

        if (! $this->arrayStringAndFnMatcher->isMatch($funcName, $this->forbiddenFunctions)) {
            return [];
        }

        // special cases
        if ($this->shouldAllowSpecialCase($node, $scope, $funcName)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $funcName)];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    return eval('...');
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    return echo '...';
}
CODE_SAMPLE
            ,
                [
                    'forbiddenFunctions' => ['eval'],
                ]
            ),
        ]);
    }

    private function shouldAllowSpecialCase(FuncCall $funcCall, Scope $scope, string $functionName): bool
    {
        if ($functionName !== 'property_exists') {
            return false;
        }

        $firstArgValue = $funcCall->args[0]->value;
        $firstArgType = $scope->getType($firstArgValue);

        return $this->objectTypeAnalyzer->isObjectOrUnionOfObjectType($firstArgType, SimpleXMLElement::class);
    }
}
