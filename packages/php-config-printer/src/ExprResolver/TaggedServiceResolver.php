<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use PhpParser\Node\Expr;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\ValueObject\FunctionName;

final class TaggedServiceResolver
{
    /**
     * @var ServiceReferenceExprResolver
     */
    private $serviceReferenceExprResolver;

    public function __construct(ServiceReferenceExprResolver $serviceReferenceExprResolver)
    {
        $this->serviceReferenceExprResolver = $serviceReferenceExprResolver;
    }

    public function resolve(TaggedValue $taggedValue): Expr
    {
        $serviceName = $taggedValue->getValue()['class'];
        $functionName = FunctionName::INLINE_SERVICE;
        return $this->serviceReferenceExprResolver->resolveServiceReferenceExpr($serviceName, false, $functionName);
    }
}
