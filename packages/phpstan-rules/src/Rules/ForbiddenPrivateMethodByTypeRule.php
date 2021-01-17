<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule\ForbiddenPrivateMethodByTypeRuleTest
 */
final class ForbiddenPrivateMethodByTypeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class';

    /**
     * @var array<string, string>
     */
    private $forbiddenTypes = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @param array<string, string> $forbiddenTypes
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        ParentNodeFinder $parentNodeFinder,
        array $forbiddenTypes = []
    ) {
        $this->forbiddenTypes = $forbiddenTypes;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isPrivate()) {
            return [];
        }

        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        $class = $this->parentNodeFinder->getFirstParentByType($node, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        if ($class->isAbstract()) {
            return [];
        }

        foreach ($this->forbiddenTypes as $forbiddenType) {
            if (! is_a($className, $forbiddenType, true)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeCommand extends Command
{
    public function run()
    {
        $this->somePrivateMethod();
    }

    private function somePrivateMethod()
    {
        // ...
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeCommand extends Command
{
    /**
     * @var ExternalService
     */
    private $externalService;

    public function __construct(ExternalService $externalService)
    {
        $this->externalService = $externalService;
    }

    public function run()
    {
        $this->externalService->someMethod();
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenTypes' => ['Command'],
                ]
            ),
        ]);
    }
}
