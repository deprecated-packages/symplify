<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use Symfony\Component\Finder\Finder;
use Symplify\Astral\NodeValue\NodeValueResolver;

final class PathResolver
{
    public function __construct(
        private NodeValueResolver $nodeValueResolver
    ) {
    }

    public function resolveExistingFilePath(Expr $expr, Scope $scope): ?string
    {
        $resolvedTemplateFilePath = $this->nodeValueResolver->resolveWithScope($expr, $scope);

        // file could not be found, nothing we can do
        if (! is_string($resolvedTemplateFilePath)) {
            return null;
        }

        if (file_exists($resolvedTemplateFilePath)) {
            return $resolvedTemplateFilePath;
        }

        $filePath = $this->findCandidateInTemplatesDirectory($resolvedTemplateFilePath);
        if ($filePath === null) {
            return null;
        }

        $fileRealPath = realpath($filePath);
        if ($fileRealPath === false) {
            return null;
        }

        return $fileRealPath;
    }

    /**
     * Helps with mapping of short name to FQN template name; Make configurable via rule constructor?
     */
    private function findCandidateInTemplatesDirectory(string $resolvedTemplateFilePath): string|null
    {
        $symfonyTemplatesDirectory = getcwd() . '/templates';
        if (! file_exists($symfonyTemplatesDirectory)) {
            return null;
        }

        $finder = new Finder();
        $finder->in($symfonyTemplatesDirectory)
            ->files()
            ->name('*.twig');

        foreach ($finder->getIterator() as $fileInfo) {
            if (str_ends_with($fileInfo->getRealPath(), $resolvedTemplateFilePath)) {
                return $fileInfo->getRealPath();
            }
        }

        return null;
    }
}
