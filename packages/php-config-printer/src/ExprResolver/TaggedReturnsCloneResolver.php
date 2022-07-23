<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\ValueObject\FunctionName;

final class TaggedReturnsCloneResolver
{
    public function __construct(
        private ServiceReferenceExprResolver $serviceReferenceExprResolver
    ) {
    }

    public function resolve(TaggedValue $taggedValue): Array_
    {
        $serviceName = $taggedValue->getValue()[0];

        $expr = $this->serviceReferenceExprResolver->resolveServiceReferenceExpr(
            $serviceName,
            false,
            FunctionName::SERVICE
        );

        return new Array_([new ArrayItem($expr)]);
    }
}
