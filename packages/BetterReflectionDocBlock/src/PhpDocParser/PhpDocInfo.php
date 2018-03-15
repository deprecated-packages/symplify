<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\PhpDocParser;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;

final class PhpDocInfo
{
    /**
     * @var PhpDocNode
     */
    private $phpDocNode;

    /**
     * @var bool
     */
    private $isSingleLineDoc;

    public function __construct(PhpDocNode $phpDocNode, bool $isSingleLineDoc)
    {
        $this->phpDocNode = $phpDocNode;
        $this->isSingleLineDoc = $isSingleLineDoc;
    }

    public function __toString(): string
    {
        if ($this->isSingleLineDoc) {
            return sprintf('/** %s */', implode(' ', $this->phpDocNode->children));
        }

        $start = '/**' . PHP_EOL;
        $end = ' */' . PHP_EOL;

        $middle = '';
        foreach ($this->phpDocNode->children as $childNode) {
            if ($childNode instanceof PhpDocTextNode && $childNode->text === '') {
                $middle .= ' *' . PHP_EOL;
            } else {
                $middle .= ' * ' . (string) $childNode . PHP_EOL;
            }
        }

        return $start . $middle . $end;
    }

    public function getPhpDocNode(): PhpDocNode
    {
        return $this->phpDocNode;
    }

    public function isSingleLineDoc(): bool
    {
        return $this->isSingleLineDoc;
    }
}
