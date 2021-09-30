<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
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
     * @param array<class-string> $forbiddenTypes
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private array $forbiddenTypes
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [InClassNode::class];
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();

        if (! $this->isClassWithPrivateMethod($classLike)) {
            return [];
        }

        $className = $this->simpleNameResolver->getClassNameFromScope($scope);
        if ($className === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $this->isClassReflectionOfTypes($classReflection, $this->forbiddenTypes)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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

    private function isClassWithPrivateMethod(ClassLike $classLike): bool
    {
        if (! $classLike instanceof Class_) {
            return false;
        }

        if ($classLike->isAbstract()) {
            return false;
        }

        foreach ($classLike->getMethods() as $classMethod) {
            if ($classMethod->isPrivate()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<class-string> $types
     */
    private function isClassReflectionOfTypes(ClassReflection $classReflection, array $types): bool
    {
        foreach ($types as $type) {
            if ($classReflection->isSubclassOf($type)) {
                return true;
            }

            if ($classReflection->implementsInterface($type)) {
                return true;
            }
        }

        return false;
    }
}
