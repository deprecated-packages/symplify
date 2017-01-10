<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;


/**
 * Rules:
 * - DocBlock lines should start with space (except first one)
 */
final class DocBlockSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var string
	 */
	const NAME = 'SymplifyCodingStandard.WhiteSpace.DocBlock';

	/**
	 * @var PHP_CodeSniffer_File
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
	 * @return int[]
	 */
	public function register() : array
	{
		return [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG];
	}


	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 */
	public function process(PHP_CodeSniffer_File $file, $position) : void
	{
		$this->file = $file;
		$this->position = $position;
		$this->tokens = $file->getTokens();

		if ($this->isInlineComment()) {
			return;
		}

		if ( ! $this->isIndentationInFrontCorrect()) {
			$file->addError('DocBlock lines should start with space (except first one)', $position);
		}

		if ( ! $this->isIndentationInsideCorrect()) {
			$file->addError('Indentation in DocBlock should be one space followed by tabs (if necessary)', $position);
		}
	}


	private function isInlineComment() : bool
	{
		if ($this->tokens[$this->position - 1]['code'] !== T_DOC_COMMENT_WHITESPACE) {
			return TRUE;
		}
		return FALSE;
	}


	private function isIndentationInFrontCorrect() : bool
	{
		$tokens = $this->file->getTokens();
		if ($tokens[$this->position - 1]['content'] === ' ') {
			return TRUE;
		}
		if ((strlen($tokens[$this->position - 1]['content']) % 2) === 0) {
			return TRUE;
		}
		return FALSE;
	}


	private function isIndentationInsideCorrect() : bool
	{
		$tokens = $this->file->getTokens();
		if ($tokens[$this->position + 1]['code'] === 'PHPCS_T_DOC_COMMENT_WHITESPACE') {
			$content = $tokens[$this->position + 1]['content'];
			$content = rtrim($content, "\n");
			if ( strlen($content) > 1
				&& $content !== ' ' . str_repeat("\t", strlen($content) - 1)
			) {
				return FALSE;
			}
		}
		return TRUE;
	}

}
