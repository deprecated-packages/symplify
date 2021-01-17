<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\TypeResolver;

use Nette\Utils\Reflection;
use ReflectionMethod;
use Symplify\AutowireArrayParameter\DocBlock\ParamTypeDocBlockResolver;

final class ParameterTypeResolver
{
    /**
     * @var ParamTypeDocBlockResolver
     */
    private $paramTypeDocBlockResolver;

    public function __construct(ParamTypeDocBlockResolver $paramTypeDocBlockResolver)
    {
        $this->paramTypeDocBlockResolver = $paramTypeDocBlockResolver;
    }

    public function resolveParameterType(string $parameterName, ReflectionMethod $reflectionMethod): ?string
    {
        // @todo apply cache here to avoid double resolving

        $docComment = $reflectionMethod->getDocComment();
        if ($docComment === false) {
            return null;
        }

        $resolvedType = $this->paramTypeDocBlockResolver->resolve($docComment, $parameterName);

        if ($resolvedType === null) {
            return null;
        }

        // not a class|interface type
        if (ctype_lower($resolvedType[0])) {
            return null;
        }

        return Reflection::expandClassName($resolvedType, $reflectionMethod->getDeclaringClass());
    }
}
