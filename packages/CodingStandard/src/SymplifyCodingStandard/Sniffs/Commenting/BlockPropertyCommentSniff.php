<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;


/**
 * Rules:
 * - Block comment should be used instead of one liner.
 */
final class BlockPropertyCommentSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var string
	 */
	const NAME = 'SymplifyCodingStandard.Commenting.BlockPropertyComment';

	/**
	 * @var PHP_CodeSniffer_File
	 */
	private $file;

	/**
	 * @var array
	 */
	private $tokens;


	/**
	 * @return int[]
	 */
	public function register() : array
	{
		return [T_DOC_COMMENT_OPEN_TAG];
	}


	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		$this->file = $file;
		$this->tokens = $file->getTokens();

		$closeTagPosition = $file->findNext(T_DOC_COMMENT_CLOSE_TAG, $position + 1);
		if ($this->isPropertyOrMethodComment($closeTagPosition) === FALSE) {
			return;

		} elseif ($this->isSingleLineDoc($position, $closeTagPosition) === FALSE) {
			return;
		}

		$fix = $file->addFixableError('Block comment should be used instead of one liner', $position);

		if ($fix) {
			$this->changeSingleLineDocToDocBlock($position);
		}
	}


	private function isPropertyOrMethodComment(int $position) : bool
	{
		$nextPropertyOrMethodPosition = $this->file->findNext([T_VARIABLE, T_FUNCTION], $position + 1);

		if ($nextPropertyOrMethodPosition && $this->tokens[$nextPropertyOrMethodPosition]['code'] !== T_FUNCTION) {
			if ($this->isVariableOrPropertyUse($nextPropertyOrMethodPosition) === TRUE) {
				return FALSE;
			}

			if (($this->tokens[$position]['line'] + 1) === $this->tokens[$nextPropertyOrMethodPosition]['line']) {
				return TRUE;
			}
		}

		return FALSE;
	}


	private function isSingleLineDoc(int $openTagPosition, int $closeTagPosition) : bool
	{
		$lines = $this->tokens[$closeTagPosition]['line'] - $this->tokens[$openTagPosition]['line'];
		if ($lines < 2) {
			return TRUE;
		}
		return FALSE;
	}


	private function isVariableOrPropertyUse(int $position) : bool
	{
		$previous = $this->file->findPrevious(T_OPEN_CURLY_BRACKET, $position);
		if ($previous) {
			$previous = $this->file->findPrevious(T_OPEN_CURLY_BRACKET, $previous - 1);
			if ($this->tokens[$previous]['code'] === T_OPEN_CURLY_BRACKET) { // used in method
				return TRUE;
			}
		}
		return FALSE;
	}


	private function changeSingleLineDocToDocBlock(int $position)
	{
		$commentEndPosition = $this->tokens[$position]['comment_closer'];

		$empty = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];
		$shortPosition = $this->file->findNext($empty, $position + 1, $commentEndPosition, TRUE);

		// indent content after /** to indented new line
		$this->file->fixer->addContentBefore($shortPosition, PHP_EOL . "\t" . ' * ');

		// remove spaces
		$this->file->fixer->replaceToken($position + 1, '');
		$spacelessContent = trim($this->tokens[$commentEndPosition - 1]['content']);
		$this->file->fixer->replaceToken($commentEndPosition - 1, $spacelessContent);

		// indent end to indented newline
		$this->file->fixer->replaceToken($commentEndPosition, PHP_EOL . "\t" . ' */');
	}

}
