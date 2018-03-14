<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Symplify\BetterReflectionDocBlock\Tag\TolerantParam;
use Symplify\BetterReflectionDocBlock\Tag\TolerantReturn;
use Symplify\BetterReflectionDocBlock\Tag\TolerantVar;

/**
 * Same as DocBlockFactory::instance(), but uses only tags that are needed
 */
final class CleanDocBlockFactory
{
    /**
     * @var DocBlockFactory
     */
    private $phpDocumentorDocBlockFactory;

    public function __construct()
    {
        $fqsenResolver = new FqsenResolver();
        $tagFactory = new StandardTagFactory($fqsenResolver, [
            'param' => TolerantParam::class,
            'return' => TolerantReturn::class,
            'var' => TolerantVar::class,
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
