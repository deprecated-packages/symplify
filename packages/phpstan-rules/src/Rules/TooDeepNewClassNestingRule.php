<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\TooDeepNewClassNestingRuleTest
 */
final class TooDeepNewClassNestingRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'new <class> is limited to %d "new <class>(new <class>))" nesting to each other. You have %d nesting.';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var int
     */
    private $maxNewClassNesting;

    public function __construct(NodeFinder $nodeFinder, int $maxNewClassNesting = 3)
    {
        $this->nodeFinder = $nodeFinder;
        $this->maxNewClassNesting = $maxNewClassNesting;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $countNew = count($this->nodeFinder->findInstanceOf($node, New_::class));

        if ($this->maxNewClassNesting >= $countNew) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $this->maxNewClassNesting, $countNew);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$someObject = new A(
    new B(
        new C()
    )
);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$firstObject = new B(new C());
$someObject = new A($firstObject);
CODE_SAMPLE
                ,
                [
                    'maxNewClassNesting' => 2,
                ]
            ),
        ]);
    }
}
