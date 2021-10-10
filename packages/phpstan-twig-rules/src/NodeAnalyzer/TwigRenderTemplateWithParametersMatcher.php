<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\TemplatePHPStanCompiler\NodeAnalyzer\PathResolver;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

final class TwigRenderTemplateWithParametersMatcher
{
    public function __construct(
        private PathResolver $pathResolver
    ) {
    }

    /**
     * Must be template path + variables
     */
    public function match(
        MethodCall $methodCall,
        Scope $scope,
        string $templateSuffix
    ): RenderTemplateWithParameters|null {
        $firstArg = $methodCall->args[0] ?? null;
        if ($firstArg === null) {
            return null;
        }

        $firstArgValue = $firstArg->value;

        $resolvedTemplateFilePaths = $this->pathResolver->resolveExistingFilePaths(
            $firstArgValue,
            $scope,
            $templateSuffix
        );
        if ($resolvedTemplateFilePaths === []) {
            return null;
        }

        $parametersArray = $this->resolveParametersArray($methodCall);
        return new RenderTemplateWithParameters($resolvedTemplateFilePaths, $parametersArray);
    }

    private function resolveParametersArray(MethodCall $methodCall): Array_
    {
        if (count($methodCall->args) !== 2) {
            return new Array_();
        }

        $secondArgValue = $methodCall->args[1]->value;
        if (! $secondArgValue instanceof Array_) {
            return new Array_();
        }

        return $secondArgValue;
    }
}
