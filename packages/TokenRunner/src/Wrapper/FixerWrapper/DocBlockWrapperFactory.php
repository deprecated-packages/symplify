<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeResolver;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;

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

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    public function __construct(
        PhpDocInfoFactory $phpDocInfoFactory,
        PhpDocInfoPrinter $phpDocInfoPrinter,
        TypeResolver $typeResolver,
        PhpDocModifier $phpDocModifier
    ) {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->phpDocInfoPrinter = $phpDocInfoPrinter;
        $this->typeResolver = $typeResolver;
        $this->phpDocModifier = $phpDocModifier;
    }

    public function create(Tokens $tokens, int $position, string $content): DocBlockWrapper
    {
        return new DocBlockWrapper(
            $tokens,
            $position,
            $this->phpDocInfoFactory->createFrom($content),
            $this->phpDocInfoPrinter,
            $this->typeResolver,
            $this->phpDocModifier
        );
    }
}
