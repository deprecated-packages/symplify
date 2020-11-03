<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symplify\CodingStandard\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRuleTest
 */
final class CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'autowire(), autoconfigure(), and public() are required in config service';

    /**
     * @var string[]
     */
    private const REQUIRED_METHODS = ['autowire', 'autoconfigure', 'public'];

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node->var);
        if (! $type instanceof ObjectType) {
            return [];
        }

        $className = $type->getClassName();
        if (! is_a($className, ServicesConfigurator::class, true)) {
            return [];
        }

        /** @var Identifier $methodIdentifier */
        $methodIdentifier = $node->name;

        // ensure start with ->defaults()
        if ($methodIdentifier->toString() !== 'defaults') {
            return [];
        }

        $methodCallNames = $this->getMethodCallNames($node);
        foreach (self::REQUIRED_METHODS as $method) {
            if (! in_array($method, $methodCallNames, true)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getMethodCallNames(MethodCall $methodCall): array
    {
        $methodCalls = [];
        while ($methodCall) {
            if ($methodCall instanceof MethodCall && $methodCall->name instanceof Identifier) {
                $methodCalls[] = $methodCall->name->toString();
            }

            $methodCall = $methodCall->getAttribute(PHPStanAttributeKey::PARENT);
        }

        return $methodCalls;
    }
}
