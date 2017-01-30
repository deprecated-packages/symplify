<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\PhpGenerator\Method;
use Nette\Utils\Strings;
use PHP_CodeSniffer_File;
use Symplify\CodingStandard\Helper\TokenFinder;

final class ParameterWrapper
{
    /**
     * @var PHP_CodeSniffer_File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $tokens;

    public static function createFromFileAndPosition(PHP_CodeSniffer_File $file, int $position)
    {
        return new self($file, $position);
    }

    private function __construct(PHP_CodeSniffer_File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $this->file->getTokens();
    }

    public function getParamterName() : string
    {
        $namePosition = $this->file->findNext(T_STRING, $this->position);

        return $this->tokens[$namePosition]['content'];
    }

    public function getParamterType() : string
    {
        $typePosition = $this->file->findPrevious(T_STRING, $this->position);

        return $this->tokens[$typePosition]['content'];
    }
}
