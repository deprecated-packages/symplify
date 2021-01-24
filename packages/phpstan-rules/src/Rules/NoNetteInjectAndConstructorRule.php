<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDefaultParameterValueRule\NoDefaultParameterValueRuleTest
 */
final class NoNetteInjectAndConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use either __consturct() or injects, not both';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethodsNode::class];
    }

    /**
     * @param ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        dump($node);
        die;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $someType;

    public function __construct()
    {
        // ...
    }

    public function injectSomeType($someType)
    {
        $this->someType = $someType;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $someType;

    public function __construct($someType)
    {
        $this->someType = $someType;
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
