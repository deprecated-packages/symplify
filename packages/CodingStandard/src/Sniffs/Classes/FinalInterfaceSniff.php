<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Classes;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\TokenRunner\Naming\Name\NameFactory;

// @todo: turn in fixer!!!

final class FinalInterfaceSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Non-abstract class that implements interface should be final.';

    /**
     * @var string[]
     */
    public $onlyInterfaces = ['EventSubscriber'];

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->fixer = $file->fixer;
        $this->position = $position;

        if ($this->shouldBeSkipped()) {
            return;
        }

        $fix = $file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
        if ($fix) {
            $this->addFinalToClass($position);
        }
    }

    private function addFinalToClass(int $position): void
    {
        $this->fixer->addContentBefore($position, 'final ');
    }

    private function shouldBeSkipped(): bool
    {
        if ($this->implementsInterface() === false) {
            return true;
        }

        if ($this->isFinalOrAbstractClass()) {
            return true;
        }

        if ($this->isDoctrineEntity()) {
            return true;
        }


        if ($this->onlyInterfaces) {
            $interfacePosition = $this->file->findNext(T_IMPLEMENTS, $this->position + 2);$interfacePosition =                          nameStart = $this->file->findNext(T_IMPLEMENTS, $interfacePosition);
            // ...
            $name = NameFactory::createFromFileAndStart($this->file, $interfacePosition);
            dump($name);
            die;

//            NameFactory::createFromTokensAndStart('...');
        }

        return false;
    }

    private function implementsInterface(): bool
    {
        // exclusiveInterfaces

        return (bool) $this->file->findNext(T_IMPLEMENTS, $this->position);
    }

    private function isFinalOrAbstractClass(): bool
    {
        $classProperties = $this->file->getClassProperties($this->position);

        return $classProperties['is_abstract'] || $classProperties['is_final'];
    }

    private function isDoctrineEntity(): bool
    {
        $docCommentPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->position);
        if ($docCommentPosition === false) {
            return false;
        }

        $seekPosition = (int) $docCommentPosition;

        do {
            $docCommentTokenContent = $this->file->getTokens()[$docCommentPosition]['content'];
            if (Strings::contains($docCommentTokenContent, 'Entity')) {
                return true;
            }

            ++$seekPosition;
            $docCommentPosition = $this->file->findNext(T_DOC_COMMENT_TAG, $seekPosition, $this->position);
        } while ($docCommentPosition);

        return false;
    }
}
