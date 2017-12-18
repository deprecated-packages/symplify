<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use Symplify\GitWrapper\Exception\GitException;

/**
 * All commands executed via an instance of this class act on the working copy that is set through the constructor
 */
final class GitWorkingCopy
{
    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var string
     */
    private $directory;

    /**
     * The output captured by the last run Git commnd(s).
     *
     * @var string
     */
    private $output = '';

    /**
     * If the variable is null, the a rudimentary check will be performed to see
     * if the directory looks like it is a working copy.
     *
     * @var bool|null
     */
    private $isCloned;

    public function __construct(GitWrapper $gitWrapper, string $directory)
    {
        $this->gitWrapper = $gitWrapper;
        $this->directory = $directory;
    }

    /**
     * Gets the output captured by the last run Git command(s).
     */
    public function __toString(): string
    {
        return $this->getOutput();
    }

    public function getWrapper(): GitWrapper
    {
        return $this->gitWrapper;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Gets the output captured by the last run Git commnd(s).
     */
    public function getOutput(): string
    {
        $output = $this->output;
        $this->clearOutput();
        return $output;
    }

    public function clearOutput(): void
    {
        $this->output = '';
    }

    public function setIsCloned(bool $isCloned): void
    {
        $this->isCloned = $isCloned;
    }

    /**
     * If the flag is not set, test if it looks like we're at a git directory.
     */
    public function isCloned(): bool
    {
        if ($this->isCloned === null) {
            $gitDir = $this->directory;
            if (is_dir($gitDir . '/.git')) {
                $gitDir .= '/.git';
            }

            $this->isCloned = (is_dir($gitDir . '/objects') && is_dir($gitDir . '/refs') && is_file($gitDir . '/HEAD'));
        }

        return $this->isCloned;
    }

    /**
     * Runs a Git command and captures the output.
     *
     * @param mixed[] $argsAndOptions
     */
    public function run(string $command, array $argsAndOptions = [], bool $setDirectory = true): string
    {
        $command = new GitCommand($command, ...$argsAndOptions);
        if ($setDirectory) {
            $command->setDirectory($this->directory);
        }

        $this->output .= $this->gitWrapper->run($command);

        return $this->getOutput();
    }

    /**
     * Returns the output of a `git status -s` command.
     */
    public function getStatus(): string
    {
        return $this->gitWrapper->git('status -s', $this->directory);
    }

    public function hasChanges(): bool
    {
        $output = $this->getStatus();

        return ! empty($output);
    }

    /**
     * Returns whether HEAD has a remote tracking branch
     */
    public function isTracking(): bool
    {
        try {
            $this->run('rev-parse', ['@{u}']);
        } catch (GitException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Returns a GitBranches object containing information on the repository's
     * branches.
     */
    public function getBranches(): GitBranches
    {
        return new GitBranches($this);
    }

    /**
     * This is synonymous with `git push origin tag v1.2.3`.
     *
     * @param string $repository The destination of the push operation, which is either a URL or name of the remote.
     * @param mixed[] $options
     */
    public function pushTag(string $tag, string $repository = 'origin', array $options = []): string
    {
        return $this->push($repository, 'tag', $tag, $options);
    }

    /**
     * Helper method that pushes all tags to a repository.
     *
     * This is synonymous with `git push --tags origin`.
     *
     * @param string $repository The destination of the push operation, which is either a URL or name of the remote.
     * @param mixed[] $options
     */
    public function pushTags(string $repository = 'origin', array $options = []): string
    {
        $options['tags'] = true;
        return $this->push($repository, $options);
    }

    /**
     * This is synonymous with `git fetch --all`
     *
     * @param mixed[] $options
     */
    public function fetchAll(array $options = []): string
    {
        $options['all'] = true;

        return trim($this->fetch($options));
    }

    /**
     * This is synonymous with `git checkout -b`
     *
     * @param mixed[] $options
     */
    public function checkoutNewBranch(string $branch, array $options = []): string
    {
        $options['b'] = true;

        return $this->checkout($branch, $options);
    }

    /**
     * @param mixed[] $options An associative array of options, with the following keys:
     *   -f: Boolean, set to true to run git fetch immediately after the remote is set up. Defaults to false.
     *
     *   --tags: Boolean. By default only the tags from the fetched branches are imported when git fetch is run.
     *      Set this to true to import every tag from the remote repository. Defaults to false.
     *
     *   --no-tags: Boolean, when set to true, git fetch does not import tags from the remote repository.
     *      Defaults to false.
     *
     *   -t: Optional array of branch names to track. If left empty, all
     *     branches will be tracked.
     *
     *   -m: Optional name of the master branch to track. This will set up a symbolic ref 'refs/remotes/<name>/HEAD
     *      which points at the specified master branch on the remote. When omitted, no symbolic ref will be created.
     */
    public function addRemote(string $name, string $url, array $options = []): void
    {
        $this->ensureAddRemoveArgsAreValid($name, $url);

        $argsAndOptions = ['add'];

        // Add boolean options
        foreach (['-f', '--tags', '--no-tags'] as $option) {
            if (! empty($options[$option])) {
                $argsAndOptions[] = $option;
            }
        }

        // Add tracking branches
        if (! empty($options['-t'])) {
            foreach ($options['-t'] as $branch) {
                array_push($argsAndOptions, '-t', $branch);
            }
        }

        // Add master branch
        if (! empty($options['-m'])) {
            array_push($argsAndOptions, '-m', $options['-m']);
        }

        // Add remote name and URL.
        array_push($argsAndOptions, $name, $url);

        $this->run('remote', $argsAndOptions);
    }

    public function removeRemote(string $name): void
    {
        $this->remote('rm', $name);
    }

    public function hasRemote(string $name): bool
    {
        return array_key_exists($name, $this->getRemotes());
    }

    /**
     * @return mixed[] An associative array with the following keys:
     *  - fetch: the fetch URL
     *  - push: the push URL
     */
    public function getRemote(string $name): array
    {
        if (! $this->hasRemote($name)) {
            throw new GitException(sprintf('The remote "%s" does not exist.', $name));
        }

        $remotes = $this->getRemotes();
        return $remotes[$name];
    }

    /**
     * @return mixed[] An associative array, keyed by remote name, containing an associative
     *   array with the following keys:
     *   - fetch: the fetch URL.
     *   - push: the push URL.
     */
    public function getRemotes(): array
    {
        $this->clearOutput();

        $remotes = [];

        foreach (explode(PHP_EOL, rtrim($this->remote())) as $remote) {
            $remotes[$remote]['fetch'] = $this->getRemoteUrl($remote);
            $remotes[$remote]['push'] = $this->getRemoteUrl($remote, 'push');
        }

        return $remotes;
    }

    /**
     * Returns the fetch or push URL of a given remote.
     *
     * @param string $operation The operation for which to return the remote. Can be either 'fetch' or 'push'.
     */
    public function getRemoteUrl(string $remote, string $operation = 'fetch'): string
    {
        $this->clearOutput();

        if ($operation === 'push') {
            return $this->remote('get-url', '--push', $remote);
        }

        return $this->remote('get-url', $remote);
    }

    /**
     * @code $git->add('some/file.txt');
     *
     * @param mixed[] $options
     */
    public function add(string $filePattern, array $options = []): void
    {
        $argsAndOptions = [$filePattern, $options];

        $this->run('add', $argsAndOptions);
    }

    /**
     * @code $git->apply('the/file/to/read/the/patch/from');
     *
     * @param mixed ...$argsAndOptions
     */
    public function apply(...$argsAndOptions): string
    {
        return $this->run('apply', $argsAndOptions);
    }

    /**
     * @code $git->bisect('good', '2.6.13-rc2');
     * $git->bisect('view', ['stat' => true]);
     *
     * @param mixed ...$argsAndOptions
     */
    public function bisect(string $subCommand, ...$argsAndOptions): string
    {
        $argsAndOptions = [$subCommand] + $argsAndOptions;
        return $this->run('bisect', $argsAndOptions);
    }

    /**
     * @code $git->branch('my2.6.14', 'v2.6.14');
     * $git->branch('origin/html', 'origin/man', ['d' => true, 'r' => 'origin/todo']);
     *
     * @param mixed ...$argsAndOptions
     */
    public function branch(...$argsAndOptions): string
    {
        return $this->run('branch', $argsAndOptions);
    }

    /**
     * @code $git->checkout('new-branch', ['b' => true]);
     *
     * @param mixed ...$argsAndOptions
     */
    public function checkout(...$argsAndOptions): string
    {
        return $this->run('checkout', $argsAndOptions);
    }

    /**
     * Clone a repository into a new directory. Use GitWorkingCopy::clone()
     * instead for more readable code.
     *
     * @code $git->cloneRepository('git://github.com/cpliakas/git-wrapper.git');
     */
    public function cloneRepository(string $repository, string ...$options): void
    {
        $argsAndOptions = [$repository, $this->directory, $options];
        $this->run('clone', $argsAndOptions, false);
    }

    /**
     * Record changes to the repository. If only one argument is passed, it is
     * assumed to be the commit message. Therefore `$git->commit('Message');`
     * yields a `git commit -am "Message"` command.
     *
     * @code $git->commit('My commit message');
     * $git->commit('Makefile', ['m' => 'My commit message']);
     *
     * @param mixed ...$argsAndOptions
     */
    public function commit(...$argsAndOptions): string
    {
        if (isset($argsAndOptions[0]) && is_string($argsAndOptions[0]) && ! isset($argsAndOptions[1])) {
            $argsAndOptions[0] = [
                'm' => $argsAndOptions[0], // message
                //'a' => true, // commit all - buggy?
            ];
        }

        return $this->run('commit', $argsAndOptions);
    }

    /**
     * Get and set repository options.
     *
     * @code $git->config('user.email', 'opensource@chrispliakas.com');
     * $git->config('user.name', 'Chris Pliakas');
     *
     * @param mixed ...$argsAndOptions
     */
    public function config(...$argsAndOptions): string
    {
        return $this->run('config', $argsAndOptions);
    }

    /**
     * @code $git->diff();
     * $git->diff('topic', 'master');
     *
     * @param mixed ...$argsAndOptions
     */
    public function diff(...$argsAndOptions): string
    {
        return $this->run('diff', $argsAndOptions);
    }

    /**
     * Download objects and refs from another repository.
     *
     * @code $git->fetch('origin');
     * $git->fetch(['all' => true]);
     *
     * @param mixed ...$argsAndOptions
     */
    public function fetch(...$argsAndOptions): string
    {
        return $this->run('fetch', $argsAndOptions);
    }

    /**
     * Print lines matching a pattern.
     *
     * @code $git->grep('time_t', '--', '*.[ch]');
     *
     * @param mixed ...$argsAndOptions
     */
    public function grep(...$argsAndOptions): string
    {
        return $this->run('grep', $argsAndOptions);
    }

    /**
     * Create an empty git repository or reinitialize an existing one.
     *
     * @code $git->init(['bare' => true]);
     *
     * @param mixed[] $options
     */
    public function init(array $options = []): void
    {
        $argsAndOptions = [$this->directory, $options];
        $this->run('init', $argsAndOptions, false);
    }

    /**
     * Show commit logs.
     *
     * @code $git->log(['no-merges' => true]);
     * $git->log('v2.6.12..', 'include/scsi', 'drivers/scsi');
     *
     * @param mixed ...$argsAndOptions
     */
    public function log(...$argsAndOptions): string
    {
        return $this->run('log', $argsAndOptions);
    }

    /**
     * Join two or more development histories together.
     * @param mixed ...$argsAndOptions
     * @code $git->merge('fixes', 'enhancements');
     */
    public function merge(...$argsAndOptions): string
    {
        return $this->run('merge', $argsAndOptions);
    }

    /**
     * Move or rename a file, a directory, or a symlink.
     *
     * @code $git->mv('orig.txt', 'dest.txt');
     *
     * @param mixed[] $options
     */
    public function mv(string $source, string $destination, array $options = []): string
    {
        $argsAndOptions = [$source, $destination, $options];
        return $this->run('mv', $argsAndOptions);
    }

    /**
     * Fetch from and merge with another repository or a local branch.
     *
     * @code $git->pull('upstream', 'master');
     *
     * @param mixed ...$argsAndOptions
     */
    public function pull(...$argsAndOptions): string
    {
        return $this->run('pull', $argsAndOptions);
    }

    /**
     * Update remote refs along with associated objects.
     * @code $git->push('upstream', 'master');
     *
     * @param mixed ...$argsAndOptions
     */
    public function push(...$argsAndOptions): string
    {
        return $this->run('push', $argsAndOptions);
    }

    /**
     * Forward-port local commits to the updated upstream head.
     * @code $git->rebase('subsystem@{1}', ['onto' => 'subsystem']);
     *
     * @param mixed ...$argsAndOptions
     */
    public function rebase(...$argsAndOptions): string
    {
        return $this->run('rebase', $argsAndOptions);
    }

    /**
     * Manage the set of repositories ("remotes") whose branches you track.
     *
     * @code $git->remote('add', 'upstream', 'git://github.com/cpliakas/git-wrapper.git');
     *
     * @param mixed ...$argsAndOptions
     */
    public function remote(...$argsAndOptions): string
    {
        return trim($this->run('remote', $argsAndOptions));
    }

    /**
     * Reset current HEAD to the specified state.
     *
     * @code $git->reset(['hard' => true]);
     *
     * @param mixed ...$argsAndOptions
     */
    public function reset(...$argsAndOptions): string
    {
        return $this->run('reset', $argsAndOptions);
    }

    /**
     * Remove files from the working tree and from the index.
     *
     * @code $git->rm('oldfile.txt');
     *
     * @param mixed[] $options
     */
    public function rm(string $filepattern, array $options = []): string
    {
        $argsAndOptions = [$filepattern, $options];
        return $this->run('rm', $argsAndOptions);
    }

    /**
     * @code $git->show('v1.0.0');
     *
     * @param string $object The names of objects to show. For a more complete list of ways to spell
     * object names, see "SPECIFYING REVISIONS" section in gitrevisions(7).
     *
     * @param mixed[] $options
     */
    public function show(string $object, array $options = []): string
    {
        $argsAndOptions = [$object, $options];
        return $this->run('show', $argsAndOptions);
    }

    /**
     * Show the working tree status.
     *
     * @code $git->status(['s' => true]);
     *
     * @param mixed ...$argsAndOptions
     */
    public function status(...$argsAndOptions): string
    {
        return $this->run('status', $argsAndOptions);
    }

    /**
     * Create, list, delete or verify a tag object signed with GPG.
     *
     * @code $git->tag('v1.0.0');
     *
     * @param mixed ...$argsAndOptions
     */
    public function tag(...$argsAndOptions): string
    {
        return $this->run('tag', $argsAndOptions);
    }

    /**
     * Remove untracked files from the working tree
     *
     * @code $git->clean('-d', '-f');
     *
     * @param mixed ...$argsAndOptions
     */
    public function clean(...$argsAndOptions): string
    {
        return $this->run('clean', $argsAndOptions);
    }

    /**
     * Create an archive of files from a named tree
     *
     * @code $git->archive('HEAD', ['o' => '/path/to/archive']);
     *
     * @param mixed ...$argsAndOptions
     */
    public function archive(...$argsAndOptions): string
    {
        return $this->run('archive', $argsAndOptions);
    }

    private function ensureAddRemoveArgsAreValid(string $name, string $url): void
    {
        if (empty($name)) {
            throw new GitException('Cannot add remote without a name.');
        }

        if (empty($url)) {
            throw new GitException('Cannot add remote without a URL.');
        }
    }
}
