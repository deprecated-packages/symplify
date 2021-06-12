<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\Configuration\SymfonyFunctionNameProvider;

final class TaggedReturnsCloneResolver
{
    public function __construct(
        private SymfonyFunctionNameProvider $symfonyFunctionNameProvider,
        private ServiceReferenceExprResolver $serviceReferenceExprResolver
    ) {
    }

    public function resolve(TaggedValue $taggedValue): Array_
    {
        $serviceName = $taggedValue->getValue()[0];
        $functionName = $this->symfonyFunctionNameProvider->provideRefOrService();
        $funcCall = $this->serviceReferenceExprResolver->resolveServiceReferenceExpr(
            $serviceName,
            false,
            $functionName
        );

        return new Array_([new ArrayItem($funcCall)]);
    }
}
