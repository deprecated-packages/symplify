<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;
use Symplify\BetterReflectionDocBlock\DocBlockSerializerFactory;

final class DocBlockWrapperFactory
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

    /**
     * @var DocBlockSerializerFactory
     */
    private $docBlockSerializerFactory;

    public function __construct(
        CleanDocBlockFactory $cleanDocBlockFactory,
        DocBlockSerializerFactory $docBlockSerializerFactory
    ) {
        $this->cleanDocBlockFactory = $cleanDocBlockFactory;
        $this->docBlockSerializerFactory = $docBlockSerializerFactory;
    }

    public function create(Tokens $tokens, int $position, string $content): DocBlockWrapper
    {
        return new DocBlockWrapper(
            $tokens,
            $position,
            $content,
            $this->cleanDocBlockFactory->create($content),
            $this->docBlockSerializerFactory
        );
    }
}
