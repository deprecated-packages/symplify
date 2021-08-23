<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallArrayResolver;

final class MissingTwigTemplateRenderVariableResolver
{
    public function __construct(
        private TwigVariableNamesResolver $twigVariableNamesResolver,
        private MethodCallArrayResolver $methodCallArrayResolver
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromTemplateAndMethodCall(
        MethodCall $methodCall,
        string $templateFilePath,
        Scope $scope
    ): array {
        $templateUsedVariableNames = $this->twigVariableNamesResolver->resolveFromFile($templateFilePath);

        $availableVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition(
            $methodCall,
            $scope,
            1
        );

        // default variables
        $availableVariableNames[] = 'app';

        $missingVariableNames = array_diff($templateUsedVariableNames, $availableVariableNames);
        return array_unique($missingVariableNames);
    }
}
