<?php

declare(strict_types=1);

namespace Symplify\GitWrapper;

use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Strings;
use Symplify\GitWrapper\ValueObject\CommandName;
use Symplify\GitWrapper\ValueObject\Regex;

/**
 * Class that parses and returnes an array of branches.
 *
 * @implements IteratorAggregate<int, string>
 */
final class GitBranches implements IteratorAggregate
{
    /**
     * @var GitWorkingCopy
     */
    private $gitWorkingCopy;

    public function __construct(GitWorkingCopy $gitWorkingCopy)
    {
        $this->gitWorkingCopy = clone $gitWorkingCopy;
        $gitWorkingCopy->branch([
            'a' => true,
        ]);
    }

    /**
     * Fetches the branches via the `git branch` command.
     *
     * @api
     * @param bool $onlyRemote Whether to fetch only remote branches, defaults to false which returns all branches.
     * @return string[]
     */
    public function fetchBranches(bool $onlyRemote = false): array
    {
        $options = $onlyRemote ? [
            'r' => true,
        ] : [
            'a' => true,
        ];
        $output = $this->gitWorkingCopy->branch($options);
        $branches = Strings::split(rtrim($output), Regex::NEWLINE_REGEX);

        return array_map(function (string $branch): string {
            return $this->trimBranch($branch);
        }, $branches);
    }

    public function trimBranch(string $branch): string
    {
        return ltrim($branch, ' *');
    }

    /**
     * @return ArrayIterator<int, string>
     */
    public function getIterator(): ArrayIterator
    {
        $branches = $this->all();
        return new ArrayIterator($branches);
    }

    /**
     * @api
     * @return string[]
     */
    public function all(): array
    {
        return $this->fetchBranches();
    }

    /**
     * @return string[]
     */
    public function remote(): array
    {
        return $this->fetchBranches(true);
    }

    /**
     * @api
     * Returns currently active branch (HEAD) of the working copy.
     */
    public function head(): string
    {
        $output = $this->gitWorkingCopy->run(CommandName::REV_PARSE, ['--abbrev-ref', 'HEAD']);
        return trim($output);
    }
}
