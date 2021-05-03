<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\NodeAnalyzer\Symfony\SymfonyPhpConfigClosureAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexArrayConfigInSetRule\ForbiddenComplexArrayConfigInSetRuleTest
 */
final class ForbiddenComplexArrayConfigInSetRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'For complex configuration use value object over array';

    /**
     * @var SymfonyPhpConfigClosureAnalyzer
     */
    private $symfonyPhpConfigClosureAnalyzer;

    /**
     * @var SimpleNodeFinder
     */
    private $simpleNodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        SymfonyPhpConfigClosureAnalyzer $symfonyPhpConfigClosureAnalyzer,
        SimpleNodeFinder $simpleNodeFinder,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->symfonyPhpConfigClosureAnalyzer = $symfonyPhpConfigClosureAnalyzer;
        $this->simpleNodeFinder = $simpleNodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ArrayItem::class];
    }

    /**
     * @param ArrayItem $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        // typical for configuration
        if (! $node->key instanceof ClassConstFetch) {
            return [];
        }

        if (! $this->symfonyPhpConfigClosureAnalyzer->isSymfonyPhpConfigScope($scope)) {
            return [];
        }

        // skip extension
        if ($this->isExtensionConfiguration($node)) {
            return [];
        }

        // simple → skip
        if (! $node->value instanceof Array_) {
            return [];
        }

        $valueArray = $node->value;
        foreach ($valueArray->items as $nestedItem) {
            if (! $nestedItem instanceof ArrayItem) {
                continue;
            }

            // way too complex
            if ($nestedItem->value instanceof Array_) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('...')
        ->call('...', [[
            'options' => ['Cake\Network\Response', ['withLocation', 'withHeader']],
        ]]);
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('...')
        ->call('...', [[
            'options' => inline_value_objects([
                new SomeValueObject('Cake\Network\Response', ['withLocation', 'withHeader']),
            ]),
        ]]);
};
CODE_SAMPLE
            ),
        ]);
    }

    private function isExtensionConfiguration(ArrayItem $arrayItem): bool
    {
        $methodCall = $this->simpleNodeFinder->findFirstParentByType($arrayItem, MethodCall::class);
        if (! $methodCall instanceof MethodCall) {
            return false;
        }

        return $this->simpleNameResolver->isName($methodCall->name, 'extension');
    }
}
