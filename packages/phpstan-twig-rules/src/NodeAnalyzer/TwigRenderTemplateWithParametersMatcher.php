<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\NodeAnalyzer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\TemplatePHPStanCompiler\NodeAnalyzer\TemplateFilePathResolver;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

final class TwigRenderTemplateWithParametersMatcher
{
    public function __construct(
        private TemplateFilePathResolver $templateFilePathResolver
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
        $firstArg = $methodCall->getArgs()[0] ?? null;
        if ($firstArg === null) {
            return null;
        }

        $firstArgValue = $firstArg->value;

        $resolvedTemplateFilePaths = $this->templateFilePathResolver->resolveExistingFilePaths(
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
        if (count($methodCall->getArgs()) !== 2) {
            return new Array_();
        }

        $secondArgValue = $methodCall->getArgs()[1]
            ->value;
        if (! $secondArgValue instanceof Array_) {
            return new Array_();
        }

        return $secondArgValue;
    }
}
