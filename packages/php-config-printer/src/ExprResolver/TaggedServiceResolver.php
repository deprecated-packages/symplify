<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use PhpParser\Node\Expr;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\ValueObject\FunctionName;

final class TaggedServiceResolver
{
    public function __construct(
        private readonly ServiceReferenceExprResolver $serviceReferenceExprResolver
    ) {
    }

    public function resolve(TaggedValue $taggedValue): Expr
    {
        $serviceName = $taggedValue->getValue()['class'];
        return $this->serviceReferenceExprResolver->resolveServiceReferenceExpr(
            $serviceName,
            false,
            FunctionName::INLINE_SERVICE
        );
    }
}
