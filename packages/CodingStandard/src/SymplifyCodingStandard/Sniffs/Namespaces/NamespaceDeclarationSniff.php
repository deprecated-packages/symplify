<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\Helper\Whitespace\ClassMetrics;
use Symplify\CodingStandard\Helper\Whitespace\WhitespaceFinder;

/**
 * Rules:
 * - There must be x empty line(s) after the namespace declaration or y empty line(s) followed by use statement.
 */
final class NamespaceDeclarationSniff implements Sniff
{
    /**
     * @var string
     */
    public const NAME = 'SymplifyCodingStandard.Namespaces.NamespaceDeclaration';

    /**
     * @var int|string
     */
    public $emptyLinesAfterNamespace = 1;

    /**
     * @var int|string
     */
    private $emptyLinesBeforeUseStatement = 1;

    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array[]
     */
    private $tokens;

    /**
     * @var ClassMetrics
     */
    private $classMetrics;

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_NAMESPACE];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position) : void
    {
        $classPosition = $file->findNext([T_CLASS, T_TRAIT, T_INTERFACE], $position);

        if (! $classPosition) {
            // there is no class, nothing to see here
            return;
        }

        $this->file = $file;
        $this->position = $position;
        $this->tokens = $file->getTokens();

        $this->fixParameterTypes();

        // prepare class metrics class
        $this->classMetrics = new ClassMetrics($file, $classPosition);

        $lineDistanceBetweenNamespaceAndFirstUseStatement =
            $this->classMetrics->getLineDistanceBetweenNamespaceAndFirstUseStatement();
        $lineDistanceBetweenClassAndNamespace =
            $this->classMetrics->getLineDistanceBetweenClassAndNamespace();

        if ($lineDistanceBetweenNamespaceAndFirstUseStatement
            || $lineDistanceBetweenNamespaceAndFirstUseStatement === 0
        ) {
            $this->processWithUseStatement($lineDistanceBetweenNamespaceAndFirstUseStatement);
        } else {
            $this->processWithoutUseStatement($lineDistanceBetweenClassAndNamespace);
        }
    }

    private function processWithoutUseStatement(int $linesToNextClass) : void
    {
        if ($linesToNextClass !== $this->emptyLinesAfterNamespace) {
            $errorMessage = sprintf(
                'There should be %s empty line(s) after the namespace declaration; %s found',
                $this->emptyLinesAfterNamespace,
                $linesToNextClass
            );

            $fix = $this->file->addFixableError($errorMessage, $this->position);
            if ($fix) {
                $this->fixSpacesFromNamespaceToClass($this->position, $linesToNextClass);
            }
        }
    }

    private function processWithUseStatement(int $linesToNextUse) : void
    {
        if ($linesToNextUse !== $this->emptyLinesBeforeUseStatement) {
            $errorMessage = sprintf(
                'There should be %s empty line(s) from namespace to use statement; %s found',
                $this->emptyLinesBeforeUseStatement,
                $linesToNextUse
            );

            $fix = $this->file->addFixableError($errorMessage, $this->position);
            if ($fix) {
                $this->fixSpacesFromNamespaceToUseStatements($this->position, $linesToNextUse);
            }
        }

        $linesToNextClass = $this->classMetrics->getLineDistanceBetweenClassAndLastUseStatement();
        if ($linesToNextClass !== $this->emptyLinesAfterNamespace) {
            $errorMessage = sprintf(
                'There should be %s empty line(s) between last use and class; %s found',
                $this->emptyLinesAfterNamespace,
                $linesToNextClass
            );

            $fix = $this->file->addFixableError($errorMessage, $this->position);
            if ($fix) {
                $this->fixSpacesFromUseStatementToClass(
                    $this->classMetrics->getLastUseStatementPosition(),
                    $linesToNextClass
                );
            }
        }
    }

    private function fixSpacesFromNamespaceToUseStatements(int $position, int $linesToNextUse) : void
    {
        $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $position);
        if ($linesToNextUse === 0) {
            $nextLinePosition -= 2;
        }

        if ($linesToNextUse < $this->emptyLinesBeforeUseStatement) {
            for ($i = $linesToNextUse; $i < $this->emptyLinesBeforeUseStatement; $i++) {
                $this->file->fixer->addContent($nextLinePosition, PHP_EOL);
            }
        } elseif ($linesToNextUse > $this->emptyLinesBeforeUseStatement) {
            for ($i = $linesToNextUse; $i > $this->emptyLinesBeforeUseStatement; $i--) {
                $this->file->fixer->replaceToken($nextLinePosition, '');
                $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $nextLinePosition);
            }
        }
    }

    private function fixSpacesFromNamespaceToClass(int $position, int $linesToClass) : void
    {
        $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $position);
        if ($linesToClass === 0) {
            $nextLinePosition = $nextLinePosition-2;
        }
        if ($linesToClass < $this->emptyLinesAfterNamespace) {
            for ($i = $linesToClass; $i < $this->emptyLinesAfterNamespace; $i++) {
                $this->file->fixer->addContent($nextLinePosition, PHP_EOL);
            }
        } elseif ($linesToClass > $this->emptyLinesAfterNamespace) {
            for ($i = $linesToClass; $i > $this->emptyLinesAfterNamespace; $i--) {
                $this->file->fixer->replaceToken($nextLinePosition, '');
                $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $nextLinePosition);
            }
        }
    }

    private function fixSpacesFromUseStatementToClass(int $position, int $linesToClass) : void
    {
        if ($linesToClass < $this->emptyLinesAfterNamespace) {
            $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $position);
            for ($i = $linesToClass; $i < $this->emptyLinesAfterNamespace; $i++) {
                $this->file->fixer->addContentBefore($nextLinePosition, PHP_EOL);
            }
        } elseif ($linesToClass > $this->emptyLinesAfterNamespace) {
            $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $position);
            for ($i = $linesToClass; $i > $this->emptyLinesAfterNamespace; $i--) {
                $this->file->fixer->replaceToken($nextLinePosition, '');
                $nextLinePosition = WhitespaceFinder::findNextEmptyLinePosition($this->file, $nextLinePosition);
            }
        }
    }

    private function fixParameterTypes() : void
    {
        // Fix type in case of rewrite in custom ruleset
        $this->emptyLinesAfterNamespace = (int) $this->emptyLinesAfterNamespace;
        $this->emptyLinesBeforeUseStatement = (int) $this->emptyLinesBeforeUseStatement;
    }
}
