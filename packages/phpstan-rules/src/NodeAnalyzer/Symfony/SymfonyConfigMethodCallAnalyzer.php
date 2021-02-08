<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Symfony;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symplify\PHPStanRules\NodeAnalyzer\TypeAndNameAnalyzer;

final class SymfonyConfigMethodCallAnalyzer
{
    /**
     * @var TypeAndNameAnalyzer
     */
    private $typeAndNameAnalyzer;

    public function __construct(TypeAndNameAnalyzer $typeAndNameAnalyzer)
    {
        $this->typeAndNameAnalyzer = $typeAndNameAnalyzer;
    }

    public function isServicesSet(MethodCall $methodCall, Scope $scope): bool
    {
        return $this->typeAndNameAnalyzer->isMethodCallTypeAndName(
            $methodCall,
            $scope,
            ServicesConfigurator::class,
            'set'
        );
    }
}
