<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class PathResolver
{
    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

    public function __construct(NodeValueResolver $nodeValueResolver)
    {
        $this->nodeValueResolver = $nodeValueResolver;
    }

    public function resolveExistingFilePath(Expr $expr, Scope $scope): ?string
    {
        $resolvedTemplateFilePath = $this->nodeValueResolver->resolve($expr, $scope->getFile());
        // file could not be found, nothing we can do
        if (! is_string($resolvedTemplateFilePath)) {
            return null;
        }

        if (! file_exists($resolvedTemplateFilePath)) {
            return null;
        }

        return $resolvedTemplateFilePath;
    }
}
