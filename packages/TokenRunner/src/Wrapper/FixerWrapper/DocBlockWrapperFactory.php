<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter;
use Symplify\BetterReflectionDocBlock\PhpDocParser\TypeResolver;

final class DocBlockWrapperFactory
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    public function __construct(
        PhpDocInfoFactory $phpDocInfoFactory,
        PhpDocInfoPrinter $phpDocInfoPrinter,
        TypeResolver $typeResolver
    ) {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
        $this->typeResolver = $typeResolver;
    }

    public function create(Tokens $tokens, int $position, string $content): DocBlockWrapper
    {
        return new DocBlockWrapper(
            $tokens,
            $position,
            $this->phpDocInfoFactory->createFrom($content),
            $this->phpDocInfoPrinter,
            $this->typeResolver
        );
    }
}
