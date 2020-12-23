<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\DocBlock;

use Nette\Utils\Strings;
use Symplify\CodingStandard\Tokens\CommentedLineTrimmer;

/**
 * Heavily inspired by
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class Decommenter
{
    /**
     * @see https://regex101.com/r/MbNMeH/1
     * @var string
     */
    private const LINE_BREAKER_REGEX = '#[\-=\#\*]{2,}#';

    /**
     * @var CommentedLineTrimmer
     */
    private $commentedLineTrimmer;

    public function __construct(CommentedLineTrimmer $commentedLineTrimmer)
    {
        $this->commentedLineTrimmer = $commentedLineTrimmer;
    }

    public function decoment(string $content): string
    {
        $lines = explode(PHP_EOL, $content);

        foreach ($lines as $key => $line) {
            $lines[$key] = $this->commentedLineTrimmer->trim($line);
        }

        $uncommentedContent = implode(PHP_EOL, $lines);
        $uncommentedContent = ltrim($uncommentedContent);

        return $this->clearContent($uncommentedContent);
    }

    /**
     * Quite a few comments use multiple dashes, equals signs etc
     * to frame comments and licence headers.
     */
    private function clearContent(string $content): string
    {
        return Strings::replace($content, self::LINE_BREAKER_REGEX, '-');
    }
}
