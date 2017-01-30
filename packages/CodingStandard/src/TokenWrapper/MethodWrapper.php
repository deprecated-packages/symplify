<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenWrapper;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\Helper\TokenFinder;

final class MethodWrapper
{
    /**
     * @var File
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

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var ParameterWrapper[]
     */
    private $parameters;

    /**
     * @var array
     */
    private $methodToken;

    public static function createFromFileAndPosition(File $file, int $position)
    {
        return new self($file, $position);
    }

    private function __construct(File $file, int $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->tokens = $this->file->getTokens();

        $namePointer = TokenFinder::findNextEffective($file, $this->position + 1);
        $this->methodName = $this->tokens[$namePointer]['content'];

        $this->methodToken = $this->tokens[$position];
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function hasNamePrefix(string $prefix) : bool
    {
        return Strings::startsWith($prefix, $this->methodName);
    }

    /**
     * @return ParameterWrapper[]
     */
    public function getParameters() : array
    {
        if ($this->parameters) {
            return $this->parameters;
        }

        $paramterPositions = TokenFinder::findAllOfType(
            $this->file, T_VARIABLE, $this->methodToken['parenthesis_opener'], $this->methodToken['parenthesis_closer']
        );

        $parameters = [];
        foreach ($paramterPositions as $paramterPosition) {
            $parameters[] = ParameterWrapper::createFromFileAndPosition($this->file, $paramterPosition);
        }

        return $this->parameters = $parameters;
    }

    public function remove()
    {

    }
}
