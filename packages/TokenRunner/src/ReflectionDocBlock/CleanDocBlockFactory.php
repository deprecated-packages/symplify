<?php declare(strict_types=1);

namespace Symplify\TokenRunner\ReflectionDocBlock;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

final class CleanDocBlockFactory
{
    /**
     * @var DocBlockFactory
     */
    private $phpDocumentorDocBlockFactory;

    public function __construct()
    {
        $this->phpDocumentorDocBlockFactory = DocBlockFactory::createInstance();
    }

    public function createFromContent(string $content): DocBlock
    {
        return $this->phpDocumentorDocBlockFactory->create($content);
    }
}
