<?php

declare(strict_types=1);

namespace Symplify\GitWrapper;

use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Strings;
use Symplify\GitWrapper\ValueObject\Regex;

/**
 * Class that parses and returnes an array of commits.
 */
final class GitCommits implements IteratorAggregate
{
    /**
     * @var GitWorkingCopy
     */
    private $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
    }

    /**
     * @return ArrayIterator<string>
     */
    public function getIterator(): ArrayIterator
    {
        $commits = $this->all();
        return new ArrayIterator($commits);
    }

    /**
     * @api
     * @return string[]
     */
    public function all(): array
    {
        return $this->fetchCommits();
    }

    /**
     * Fetches the commits via the `git log` command.
     *
     * @api
     * @return string[]
     */
    public function fetchCommits(): array
    {
        $output = $this->gitWorkingCopy->log([
                'format=%H' => '',
            ]);

        return Strings::split(trim($output), Regex::NEWLINE_REGEX);
    }
}
