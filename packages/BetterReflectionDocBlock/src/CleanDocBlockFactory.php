<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;

/**
 * Same as @see DocBlockFactory::createInstance(), but uses only tags that are needed
 */
final class CleanDocBlockFactory
{
    /**
     * @var DocBlockFactory
     */
    private $phpDocumentorDocBlockFactory;

    public function __construct(FqsenResolver $fqsenResolver)
    {
        $tagFactory = new StandardTagFactory($fqsenResolver, [
            'param' => TolerantParam::class,
            'return' => TolerantReturn::class,
            'var' => Var_::class,
        ]);

        $descriptionFactory = new DescriptionFactory($tagFactory);

        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        $this->phpDocumentorDocBlockFactory = new DocBlockFactory($descriptionFactory, $tagFactory);
    }

    public function create(string $content, ?Context $context = null): DocBlock
    {
        return $this->phpDocumentorDocBlockFactory->create($content, $context);
    }
}
