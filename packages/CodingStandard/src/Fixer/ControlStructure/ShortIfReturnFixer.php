<?php
namespace Symplify\CodingStandard\Fixer\ControlStructure;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ShortIfReturnFixer implements DefinedFixerInterface
{

	public function getDefinition(): FixerDefinitionInterface
	{
		return new FixerDefinition(
			'Short if return should be used.',
			[
				new CodeSample('<?php
if ($value === true) {
    return true;
}
return false;'
				),
			]
		);
	}

	/**
	 * Check if the fixer is a candidate for given Tokens collection.
	 *
	 * Fixer is a candidate when the collection contains tokens that may be fixed
	 * during fixer work. This could be considered as some kind of bloom filter.
	 * When this method returns true then to the Tokens collection may or may not
	 * need a fixing, but when this method returns false then the Tokens collection
	 * need no fixing for sure.
	 *
	 * @param Tokens $tokens
	 *
	 * @return bool
	 */
	public function isCandidate(Tokens $tokens)
	{
		return $tokens->isAllTokenKindsFound([T_IF, T_RETURN, T_IS_IDENTICAL]);
	}

	/**
	 * Check if fixer is risky or not.
	 *
	 * Risky fixer could change code behavior!
	 *
	 * @return bool
	 */
	public function isRisky()
	{
		return false;
	}

	/**
	 * Fixes a file.
	 *
	 * @param \SplFileInfo $file A \SplFileInfo instance
	 * @param Tokens $tokens Tokens collection
	 */
	public function fix(\SplFileInfo $file, Tokens $tokens)
	{
		foreach ($tokens as $index => $token) {
			if (! $token->isGivenKind(T_IF)) {
				continue;
			}
			$nextTokenId = $tokens->getNextNonWhitespace($index);
			$nextToken = $tokens[$nextTokenId];

			// Try to look for condition surrounded by braces, mark its start and end position
			if ($nextToken->getContent() !== '(') {
				continue;
			}
			$conditionStart = $nextTokenId;
			$conditionEnd = $this->getConditionEnd($tokens, $conditionStart);

			// Check condition body - it must consist only of "return true;"
			$tokenId = $tokens->getNextNonWhitespace($conditionEnd);
			$tokenId = $tokens->getNextNonWhitespace($tokenId);
			$tokenId = $this->checkConditionBody($tokens, $tokenId);
			if ($tokenId < 0) {
				continue;
			}

			// Check statement after condition - must be "return false;"
			$tokenId = $this->checkStatementAfterCondition($tokens, $tokenId);
			if ($tokenId < 0) {
				continue;
			}
			$lastTokenId = $tokenId;
			$shortReturnTokens = $this->createShortReturnTokens($tokens, $conditionStart, $conditionEnd);
			$tokenId = $index;
			while ($tokenId !== $lastTokenId) {
				$tokens->clearAt($tokenId);
				$tokenId++;
			}
			$tokens->clearAt($tokenId);
			$tokens->clearEmptyTokens();
			$tokens->insertAt($index, $shortReturnTokens);

		}
	}

	/**
	 * Returns the name of the fixer.
	 *
	 * The name must be all lowercase and without any spaces.
	 *
	 * @return string The name of the fixer
	 */
	public function getName()
	{
		return self::class;
	}

	/**
	 * Returns the priority of the fixer.
	 *
	 * The default priority is 0 and higher priorities are executed first.
	 *
	 * @return int
	 */
	public function getPriority()
	{
		return 0;
	}

	/**
	 * Returns true if the file is supported by this fixer.
	 *
	 * @param \SplFileInfo $file
	 *
	 * @return bool true if the file is supported by this fixer, false otherwise
	 */
	public function supports(\SplFileInfo $file)
	{
		return true;
	}

	private function getConditionEnd(Tokens $tokens, $conditionStart)
	{
		$nextTokenId = $conditionStart;
		$startingBracesCount = 1;
		while ($startingBracesCount > 0) {
			$nextTokenId = $tokens->getNextMeaningfulToken($nextTokenId);
			$nextToken = $tokens[$nextTokenId];
			if ($nextToken->getContent() === '(') {
				$startingBracesCount++;
			} elseif ($nextToken->getContent() === ')') {
				$startingBracesCount--;
			}
		}
		$conditionEnd = $tokens->getPrevMeaningfulToken($nextTokenId);
		return $conditionEnd;
	}

	private function checkConditionBody(Tokens $tokens, $tokenId)
	{
		if ($tokens[$tokenId]->getContent() !== '{') {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);

		if (!$tokens[$tokenId]->isGivenKind(T_RETURN)) {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if (strtolower($tokens[$tokenId]->getContent()) !== 'true') {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if ($tokens[$tokenId]->getContent() !== ";") {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if ($tokens[$tokenId]->getContent() !== '}') {
			return -1;
		}
		return $tokenId;
	}

	private function checkStatementAfterCondition(Tokens $tokens, $tokenId)
	{
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if (!$tokens[$tokenId]->isGivenKind(T_RETURN)) {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if (strtolower($tokens[$tokenId]->getContent()) !== 'false') {
			return -1;
		}
		$tokenId = $tokens->getNextNonWhitespace($tokenId);
		if ($tokens[$tokenId]->getContent() !== ";") {
			return -1;
		}
		return $tokenId;
	}

	private function createShortReturnTokens(Tokens $tokens, $conditionStart, $conditionEnd)
	{
		$shortReturnTokens = [
			new Token([T_RETURN, 'return']),
			new Token([T_WHITESPACE, ' ']),
		];
		$tokenId = $tokens->getNonEmptySibling($conditionStart, 1);
		while ($tokenId !== $conditionEnd) {
			$shortReturnTokens[] = $tokens[$tokenId];
			$tokenId = $tokens->getNonEmptySibling($tokenId, 1);
		}
		$shortReturnTokens[] = $tokens[$tokenId];
		$shortReturnTokens[] = new Token(";");
		return $shortReturnTokens;
	}
}
