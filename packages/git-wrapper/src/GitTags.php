<?php

declare(strict_types=1);

namespace Symplify\GitWrapper;

use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Strings;
use Symplify\GitWrapper\ValueObject\Regex;

/**
 * Class that parses and returnes an array of Tags.
 *
 * @implements IteratorAggregate<int, string>
 */
final class GitTags implements IteratorAggregate
{
    private GitWorkingCopy $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
    }

    /**
     * Fetches the Tags via the `git branch` command.
     *
     * @api
     * @return string[]
     */
    public function fetchTags(): array
    {
        $output = $this->gitWorkingCopy->tag([
            'l' => true,
        ]);

        $tags = Strings::split(rtrim($output), Regex::NEWLINE_REGEX);

        return array_map(fn (string $branch): string => $this->trimTags($branch), $tags);
    }

    /**
     * Strips unwanted characters from the branch
     */
    public function trimTags(string $branch): string
    {
        return ltrim($branch, ' *');
    }

    /**
     * @return ArrayIterator<int, string>
     */
    public function getIterator(): ArrayIterator
    {
        $tags = $this->all();
        return new ArrayIterator($tags);
    }

    /**
     * @api
     * @return string[]
     */
    public function all(): array
    {
        return $this->fetchTags();
    }
}
