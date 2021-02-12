<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Nette\LatteVariableNamesResolver;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallArrayResolver;

final class MissingTemplateRenderVariableResolver
{
    /**
     * @var LatteVariableNamesResolver
     */
    private $latteVariableNamesResolver;

    /**
     * @var MethodCallArrayResolver
     */
    private $methodCallArrayResolver;

    public function __construct(
        LatteVariableNamesResolver $latteVariableNamesResolver,
        MethodCallArrayResolver $methodCallArrayResolver
    ) {
        $this->latteVariableNamesResolver = $latteVariableNamesResolver;
        $this->methodCallArrayResolver = $methodCallArrayResolver;
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

        return array_diff($templateUsedVariableNames, $availableVariableNames);
    }
}
