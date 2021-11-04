<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\NodeAnalyzer;

use PHPStan\Analyser\Scope;
use Symplify\TemplatePHPStanCompiler\NodeAnalyzer\ParametersArrayAnalyzer;
use Symplify\TemplatePHPStanCompiler\ValueObject\RenderTemplateWithParameters;

/**
 * @api
 */
final class MissingTwigTemplateRenderVariableResolver
{
    public function __construct(
        private TwigVariableNamesResolver $twigVariableNamesResolver,
        private ParametersArrayAnalyzer $parametersArrayAnalyzer
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromTemplateAndMethodCall(
        RenderTemplateWithParameters $renderTemplateWithParameters,
        Scope $scope
    ): array {
        $templateUsedVariableNames = $this->twigVariableNamesResolver->resolveFromFilePath(
            $renderTemplateWithParameters->getTemplateFilePath()
        );

        $availableVariableNames = $this->parametersArrayAnalyzer->resolveStringKeys(
            $renderTemplateWithParameters->getParametersArray(),
            $scope
        );

        // default variables
        $availableVariableNames[] = 'app';
        $availableVariableNames[] = 'blocks';

        $missingVariableNames = array_diff($templateUsedVariableNames, $availableVariableNames);

        return array_unique($missingVariableNames);
    }
}
