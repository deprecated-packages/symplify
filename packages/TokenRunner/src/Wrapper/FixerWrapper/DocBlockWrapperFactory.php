<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter;

final class DocBlockWrapperFactory
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

//    /**
//     * @var DocBlockSerializerFactory
//     */
//    private $docBlockSerializerFactory;

    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    public function __construct(
        CleanDocBlockFactory $cleanDocBlockFactory,
//        DocBlockSerializerFactory $docBlockSerializerFactory,
        PhpDocInfoFactory $phpDocInfoFactory,
        PhpDocInfoPrinter $phpDocInfoPrinter
    ) {
        $this->cleanDocBlockFactory = $cleanDocBlockFactory;
//        $this->docBlockSerializerFactory = $docBlockSerializerFactory;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
    }

    public function create(Tokens $tokens, int $position, string $content): DocBlockWrapper
    {
        return new DocBlockWrapper(
            $tokens,
            $position,
            $content,
            $this->cleanDocBlockFactory->create($content),
//            $this->docBlockSerializerFactory,
            $this->phpDocInfoFactory->createFrom($content),
            $this->phpDocInfoPrinter
        );
    }
}
