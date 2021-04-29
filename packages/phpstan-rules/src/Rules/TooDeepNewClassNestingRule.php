<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Throw_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
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
     * @return array<class-string<Node>>
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
        $objectNews = $this->findObjectNews($node);
        $countNew = count($objectNews);

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

    /**
     * @return New_[]
     */
    private function findObjectNews(New_ $new): array
    {
        /** @var New_[] $nestedNews */
        $nestedNews = $this->nodeFinder->findInstanceOf($new, New_::class);

        $objectNews = [];

        foreach ($nestedNews as $nestedNew) {
            $parent = $nestedNew->getAttribute(PHPStanAttributeKey::PARENT);
            if ($parent instanceof Throw_) {
                continue;
            }

            $objectNews[] = $nestedNew;
        }

        return $objectNews;
    }
}
