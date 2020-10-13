<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\NodeAnalyzer\DependencyNodeAnalyzer;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\ForbiddenProtectedPropertyRuleTest
 */
final class ForbiddenProtectedPropertyRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Property with protected modifier is not allowed. Use interface instead.';

    /**
     * @var string
     * @see https://regex101.com/r/Wy4mO2/2
     */
    public const KERNEL_REGEX = '#@var\s+(\\\\Symfony\\\\Component\\\\HttpKernel\\\\)?KernelInterface\n?#';

    /**
     * @var string
     * @see https://regex101.com/r/eCXekv/3
     */
    public const CONTAINER_REGEX = '#@var\s+(\\\\Psr\\\\Container\\\\)?ContainerInterface|(\\\\Symfony\\\\Component\\\\DependencyInjection\\\\)?Container\n?$#';

    /**
     * @var DependencyNodeAnalyzer
     */
    private $dependencyNodeAnalyzer;

    public function __construct(DependencyNodeAnalyzer $dependencyNodeAnalyzer)
    {
        $this->dependencyNodeAnalyzer = $dependencyNodeAnalyzer;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Property::class, ClassConst::class];
    }

    /**
     * @param Property|ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->isProtected()) {
            return [];
        }

        if ($this->dependencyNodeAnalyzer->isInsideAbstractClassAndPassedAsDependencyViaConstructor($node)) {
            return [];
        }

        if ($this->isStaticAndContainerOrKernelType($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param Property|ClassConst $node
     */
    public function isStaticAndContainerOrKernelType(Node $node): bool
    {
        if ($node instanceof ClassConst) {
            return false;
        }

        if (! $node->isStatic()) {
            return false;
        }

        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return false;
        }

        $docCommentText = $docComment->getText();
        if (Strings::match($docCommentText, self::KERNEL_REGEX)) {
            return true;
        }
        return (bool) Strings::match($docCommentText, self::CONTAINER_REGEX);
    }
}
