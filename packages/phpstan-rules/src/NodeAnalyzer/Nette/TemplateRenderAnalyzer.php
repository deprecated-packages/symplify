<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\TypeAnalyzer\ObjectTypeAnalyzer;

final class TemplateRenderAnalyzer
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ObjectTypeAnalyzer
     */
    private $objectTypeAnalyzer;

    public function __construct(SimpleNameResolver $simpleNameResolver, ObjectTypeAnalyzer $objectTypeAnalyzer)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->objectTypeAnalyzer = $objectTypeAnalyzer;
    }

    public function isTemplateRenderMethodCall(MethodCall $methodCall, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isName($methodCall->name, 'render')) {
            return false;
        }

        $callerType = $scope->getType($methodCall->var);

        return $this->objectTypeAnalyzer->isObjectOrUnionOfObjectTypes(
            $callerType,
            [
                'Nette\Application\UI\Template',
                'Nette\Bridges\ApplicationLatte\Template',
                'Nette\Bridges\ApplicationLatte\DefaultTemplate',
            ]
        );
    }
}
