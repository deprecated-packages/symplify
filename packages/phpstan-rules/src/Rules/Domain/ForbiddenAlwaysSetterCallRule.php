<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use Nette\Utils\Arrays;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Collector\ClassMethod\FormTypeClassCollector;
use Symplify\PHPStanRules\Collector\ClassMethod\NewAndSetterCallsCollector;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @implements Rule<CollectedDataNode>
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\ForbiddenAlwaysSetterCallRuleTest
 */
final class ForbiddenAlwaysSetterCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'The "%s" class always calls "%s()" setters, better move it to constructor';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @param CollectedDataNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $newAndSetterCalls = $node->get(NewAndSetterCallsCollector::class);

        $formTypeClassesCollector = $node->get(FormTypeClassCollector::class);
        $formTypeClasses = Arrays::flatten($formTypeClassesCollector);

        $groupedCallsByClass = $this->createGrouppedCallsByClass($newAndSetterCalls);

        $ruleErrors = [];

        foreach ($groupedCallsByClass as $class => $methodCalls) {
            // skip classes that are used in form_types, they always need setters/getters
            if (in_array($class, $formTypeClasses, true)) {
                continue;
            }

            if (count($methodCalls) === 1) {
                continue;
            }

            $alwaysCalledMethodCalls = array_intersect(...$methodCalls);
            if ($alwaysCalledMethodCalls === []) {
                continue;
            }

            $ruleErrors[] = sprintf(self::ERROR_MESSAGE, $class, implode('()", "', $alwaysCalledMethodCalls));
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$firstPerson = new Person();
$firstPerson->setName('John');

$secondPerson = new Person();
$secondPerson->setName('Van');
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$firstPerson = new Person('John');

$secondPerson = new Person('Van');
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param mixed[] $collections
     * @return array<string, string[][]>
     */
    private function createGrouppedCallsByClass(array $collections): array
    {
        $groupedCallsByClass = [];

        foreach ($collections as $collection) {
            foreach ($collection as $methodCallsByVariableAndClass) {
                foreach ($methodCallsByVariableAndClass as $className => $methodCallsByVariable) {
                    foreach ($methodCallsByVariable as $methodCalls) {
                        /** @var string $className */
                        /** @var string[] $methodCalls */
                        $groupedCallsByClass[$className][] = $methodCalls;
                    }
                }
            }
        }

        return $groupedCallsByClass;
    }
}
