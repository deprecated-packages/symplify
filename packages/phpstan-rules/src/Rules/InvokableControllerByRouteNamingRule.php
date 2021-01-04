<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\InvokableControllerByRouteNamingRule\InvokableControllerByRouteNamingRuleTest
 */
final class InvokableControllerByRouteNamingRule extends AbstractSymplifyRule
{
    /**
     * @see https://regex101.com/r/ChpDsj/1
     * @var string
     */
    private const ANONYMOUS_CLASS_REGEX = '#^AnonymousClass[\w+]#';

    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use controller class name based on route name instead';

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
        $classMethodName = (string) $node->name;
        if ($classMethodName !== MethodName::INVOKE) {
            return [];
        }

        /** @var string|null $shortClassName */
        $shortClassName = $this->getShortClassName($scope);

        if ($shortClassName === null) {
            return [];
        }

        if (Strings::match($shortClassName, self::ANONYMOUS_CLASS_REGEX)) {
            return [];
        }

        /** @var string $className */
        $className = $this->resolveClassLikeName($className);
        if (strpos($className, 'Tests') !== false) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SecurityController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class LogoutController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
