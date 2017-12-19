<?php declare(strict_types=1);

namespace Symplify\TokenRunner\ReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use Symplify\TokenRunner\ReflectionDocBlock\Tag\TolerantParam;
use Symplify\TokenRunner\ReflectionDocBlock\Tag\TolerantReturn;

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
            'var' => Var_::class,
        ]);

        $descriptionFactory = new DescriptionFactory($tagFactory);

        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        $this->phpDocumentorDocBlockFactory = new DocBlockFactory($descriptionFactory, $tagFactory);
    }

    public function createFromContent(string $content): DocBlock
    {
        return $this->phpDocumentorDocBlockFactory->create($content);
    }
}
