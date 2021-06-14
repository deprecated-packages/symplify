<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\LatteVariableNamesResolver;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallArrayResolver;

final class MissingTemplateRenderVariableResolver
{
    public function __construct(
        private LatteVariableNamesResolver $latteVariableNamesResolver,
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
        $templateUsedVariableNames = $this->latteVariableNamesResolver->resolveFromFile($templateFilePath);
        $availableVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition($methodCall, $scope, 1);

        $missingVariableNames = array_diff($templateUsedVariableNames, $availableVariableNames);
        return array_unique($missingVariableNames);
    }
}
