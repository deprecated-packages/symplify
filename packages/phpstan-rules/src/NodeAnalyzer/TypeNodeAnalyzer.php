<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Property;

final class TypeNodeAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/Wy4mO2/2
     */
    private const KERNEL_REGEX = '#@var\s+(\\\\Symfony\\\\Component\\\\HttpKernel\\\\)?KernelInterface\n?#';

    /**
     * @var string
     * @see https://regex101.com/r/eCXekv/3
     */
    private const CONTAINER_REGEX = '#@var\s+(\\\\Psr\\\\Container\\\\)?ContainerInterface|(\\\\Symfony\\\\Component\\\\DependencyInjection\\\\)?Container\n?$#';

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
