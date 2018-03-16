<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\BetterReflectionDocBlock\CleanDocBlockFactory;

final class DocBlockWrapperFactory
{
    /**
     * @var CleanDocBlockFactory
     */
    private $cleanDocBlockFactory;

    public function __construct(CleanDocBlockFactory $cleanDocBlockFactory)
    {
        $this->cleanDocBlockFactory = $cleanDocBlockFactory;
    }

    public function create(Tokens $tokens, int $position, string $content): DocBlockWrapper
    {
        return new DocBlockWrapper($tokens, $position, $content, $this->cleanDocBlockFactory->create($content));
    }
}
