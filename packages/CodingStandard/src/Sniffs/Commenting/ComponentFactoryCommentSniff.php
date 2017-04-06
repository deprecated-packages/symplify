<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;

final class ComponentFactoryCommentSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'createComponent<name> method should have return type.';

    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if (! $this->isComponentFactoryMethod()) {
            return;
        }

        $returnTypeHint = FunctionHelper::findReturnTypeHint($file, $position);

        if ($returnTypeHint === null || $returnTypeHint->getTypeHint() === 'void') {
            $file->addError(self::ERROR_MESSAGE, $position, self::class);
        }
    }

    private function isComponentFactoryMethod(): bool
    {
        $functionName = $this->file->getDeclarationName($this->position);

        return strpos($functionName, 'createComponent') === 0;
    }
}
