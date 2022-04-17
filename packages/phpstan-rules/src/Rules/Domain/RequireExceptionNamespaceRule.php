<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\RequireExceptionNamespaceRule\RequireExceptionNamespaceRuleTest
 */
final class RequireExceptionNamespaceRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Exception must be located in "Exception" namespace';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// app/Controller/SomeException.php
namespace App\Controller;

final class SomeException extends Exception
{

}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// app/Exception/SomeException.php
namespace App\Exception;

final class SomeException extends Exception
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (! $classReflection->isClass()) {
            return [];
        }

        if (! $classReflection->isSubclassOf('Exception')) {
            return [];
        }

        // is class in "Exception" namespace?
        $className = $classReflection->getName();
        if (str_contains($className, '\\Exception\\')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
