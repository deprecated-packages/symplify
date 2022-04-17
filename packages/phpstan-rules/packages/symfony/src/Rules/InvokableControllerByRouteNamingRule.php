<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\InvokableControllerByRouteNamingRule\InvokableControllerByRouteNamingRuleTest
 */
final class InvokableControllerByRouteNamingRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use controller class name based on route name instead';

    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Annotation\Route';

    public function __construct(
        private SymfonyControllerAnalyzer $symfonyControllerAnalyzer,
        private AttributeFinder $attributeFinder,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->symfonyControllerAnalyzer->isInControllerClass($scope)) {
            return [];
        }

        /** @var Identifier $classMethodIdentifier */
        $classMethodIdentifier = $node->name;
        $classMethodName = (string) $classMethodIdentifier;
        if ($classMethodName !== MethodName::INVOKE) {
            return [];
        }

        if (! $this->symfonyControllerAnalyzer->isControllerActionMethod($node)) {
            return [];
        }

        $routeAttribute = $this->attributeFinder->findAttribute($node, self::ROUTE_ATTRIBUTE);
        if (! $routeAttribute instanceof Attribute) {
            return [];
        }

        return $this->validateInvokable($scope, $routeAttribute);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

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
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @return string[]
     */
    private function validateInvokable(Scope $scope, Attribute $attribute): array
    {
        foreach ($attribute->args as $arg) {
            /** @var Identifier $argIdentifier */
            $argIdentifier = $arg->name;
            $argName = (string) $argIdentifier;

            if ($argName === 'name') {
                $next = $argIdentifier->getAttribute(AttributeKey::NEXT);
                if ($next instanceof String_) {
                    return $this->validateName($scope, $next->value);
                }
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function validateName(Scope $scope, string $string): array
    {
        $shortClassName = $this->simpleNameResolver->resolveShortNameFromScope($scope);
        if ($shortClassName === null) {
            return [];
        }

        $name = \str_ends_with($shortClassName, 'Controller')
            ? substr($shortClassName, 0, -10)
            : $shortClassName;

        if (strtolower($name) === strtolower($string)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
