<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Git;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Split\Exception\UnsupportedGitVersionException;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Throwable;

/**
 * Implements the ``git-subsplit`` command.
 *
 * Based on https://github.com/dflydev/git-subsplit
 */
final class GitSubsplit
{
    /**
     * @var string
     */
    private const GIT_MIN_VERSION = '1.7.11';

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var string|null
     */
    private $gitExecPath;

    /**
     * @var string|null
     */
    private $gitVersion;

    public function __construct(SymfonyStyle $symfonyStyle, ProcessRunner $processRunner)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->processRunner = $processRunner;
    }

    public function gitVersion(): string
    {
        if ($this->gitVersion === null) {
            $output = rtrim($this->git(['--version']), "\n\r");
            $this->gitVersion = Strings::replace($output, '#^\s*git\s+version\s+(\S+)\s*$#', '\1');
        }
        return $this->gitVersion;
    }

    public function gitExecPath(): string
    {
        if ($this->gitExecPath === null) {
            $this->gitExecPath = rtrim($this->git(['--exec-path']), "\r\n");
        }
        return $this->gitExecPath;
    }

    public function subsplit(
        string $workDir,
        string $repository,
        string $fromDir,
        string $toRepo,
        ?string $branch = null,
        ?string $tag = null,
        bool $dryRun = false,
        bool $createWorkDir = false
    ): void {
        $operation = sprintf("git subsplit from '%s' subtree '%s' to repository '%s'", $repository, $fromDir, $toRepo);

        if ($fromDir === '') {
            // FIXME: throw what?
        }

        if ($toRepo === '') {
            // FIXME: throw what?
        }

        if (! $branch && ! $tag) {
            // FIXME: what?
        }

        $status = sprintf('  %s', $operation);
        $this->say($status);

        try {
            $this->ensureGitVersionSupported($this->gitVersion());
            $this->subsplitInit($workDir, $repository, $createWorkDir);
            $this->subsplitPublish($workDir, $fromDir, $toRepo, $branch, $tag, $dryRun);
        } catch (Throwable $throwable) {
            $status = sprintf('<error>✗ %s</error>', $operation);
            $this->say($status);
            throw $throwable;
        }
        $status = sprintf('<info>✓ %s</info>', $operation);
        $this->say($status);
    }

    private function subsplitInit(string $workDir, string $repository, bool $createWorkDir): void
    {
        $operation = sprintf("creating git mirror of '%s' in '%s'", $repository, $workDir);

        $status = sprintf('    %s', $operation);
        $this->say($status);

        try {
            $this->prepareWorkDir($workDir, $createWorkDir);
            $this->git(['clone', '--mirror', $repository, '.git'], false, $workDir);
            $this->git(['config', '--unset', 'core.bare'], false, $workDir);
            $this->git(['reset', '--hard'], false, $workDir);
        } catch (Throwable $throwable) {
            $status = sprintf('  <error>✗ %s</error>', $operation);
            $this->say($status);
            throw $throwable;
        }

        $operation = Strings::replace($operation, '#^creating #', 'created  ');
        $status = sprintf('  <info>✓ %s</info>', $operation);
        $this->say($status);
    }

    private function subsplitPublish(
        string $workDir,
        string $fromDir,
        string $toRepo,
        ?string $branch,
        ?string $tag,
        bool $dryRun
    ): void {
        $operation = sprintf("syncing subdirectory '%s' to repository '%s'", $fromDir, $toRepo);
        $status = sprintf('    %s', $operation);
        $this->say($status);

        try {
            $this->ensureWorkDir($workDir);
            $this->git(['remote', 'remove', 'origin'], false, $workDir);
            $this->git(['remote', 'add', 'origin', $toRepo], false, $workDir);
            if ($branch) {
                $this->splitBranch($workDir, $fromDir, $branch, $dryRun);
            }
            if ($tag) {
                $this->splitTag($workDir, $fromDir, $tag, $dryRun);
            }
        } catch (Throwable $throwable) {
            $status = sprintf('  <error>✗ %s</error>', $operation);
            $this->say($status);
            throw $throwable;
        }

        $operation = Strings::replace($operation, '#^syncing #', 'synced  ');
        $status = sprintf('  <info>✓ %s</info>', $operation);
        $this->say($status);
    }

    private function splitBranch(string $workDir, string $subdir, string $branch, bool $dryRun): void
    {
        $operation = sprintf("syncing branch '%s'", $branch);

        $localBranch = sprintf('local-%s', $branch);

        $status = sprintf('      %s (this can take a while!)', $operation);
        $this->say($status);

        try {
            $localBranchCheckout = sprintf('%s-checkout', $localBranch);
            $this->git(['checkout', '-b', $localBranchCheckout, $branch], false, $workDir);
            $this->git(['subtree', 'split', '--prefix', $subdir, '--branch', $localBranch, $branch], false, $workDir);
            $this->splitPush($workDir, $localBranch, $branch, $dryRun);
        } catch (Throwable $throwable) {
            $status = sprintf('    <error>✗ %s</error>', $operation);
            $this->say($status);
            throw $throwable;
        }

        $operation = Strings::replace($operation, '#^syncing #', 'synced  ');
        $status = sprintf('    <info>✓ %s</info>', $operation);
        $this->say($status);
    }

    private function splitTag(string $workDir, string $subdir, string $tag, bool $dryRun): void
    {
        $operation = sprintf("syncing tag '%s'", $tag);

        $localBranch = sprintf('local-%s', $tag);

        $status = sprintf('      %s (this can take a while!)', $operation);
        $this->say($status);

        try {
            $localBranchCheckout = sprintf('%s-checkout', $localBranch);
            $tagRef = sprintf('tags/%s', $tag);
            $this->git(['checkout', '-b', $localBranchCheckout, $tagRef], false, $workDir);

            $absSubdir = FileSystem::isAbsolute($subdir) ? $subdir : FileSystem::joinPaths($workDir, $subdir);
            $absSubdir = FileSystem::normalizePath($absSubdir);
            if (! is_dir($absSubdir)) {
                $reason = sprintf("          - directory '%s' is not yet present in tag '%s'", $subdir, $tag);
                $status = sprintf('    <fg=yellow>✗ %s</>', $operation);
                $this->say([$reason, $status]);
                return;
            }

            $this->git(['subtree', 'split', '--prefix', $subdir, '--branch', $localBranch, $tag], false, $workDir);

            $remoteRef = sprintf('refs/tags/%s', $tag);
            $this->splitPush($workDir, $localBranch, $remoteRef, $dryRun);
        } catch (Throwable $throwable) {
            $status = sprintf('    <error>✗ %s</error>', $operation);
            $this->say($status);
            throw $throwable;
        }

        $operation = Strings::replace($operation, '#^syncing #', 'synced  ');
        $status = sprintf('    <info>✓ %s</info>', $operation);
        $this->say($status);
    }

    private function splitPush(string $workDir, string $localBranch, string $remoteBranch, bool $dryRun): void
    {
        $push = array_merge(
            ['push', '--force'],
            ($dryRun ? ['--dry-run'] : []),
            ['origin', sprintf('%s:%s', $localBranch, $remoteBranch)]
        );
        $this->git($push, false, $workDir);
    }

    private function isGitVersionSupported(string $version): bool
    {
        return version_compare($version, self::GIT_MIN_VERSION) >= 0;
    }

    private function ensureGitVersionSupported(string $version): void
    {
        if (! $this->isGitVersionSupported($version)) {
            $message = sprintf('Git version "%s" is not supported', $version);
            $this->symfonyStyle->error($message);
            throw new UnsupportedGitVersionException($message);
        }
    }

    private function git(
        $arguments,
        bool $shouldDisplayOutput = false,
        ?string $cwd = null,
        ?array $env = null
    ): string {
        $commandLine = array_merge(['git'], $arguments);
        return $this->processRunner->run($commandLine, $shouldDisplayOutput, $cwd, $env);
    }

    private function prepareWorkDir(string $workDir, bool $create = false): void
    {
        if ($create) {
            $this->createWorkDir($workDir);
        } else {
            $this->ensureWorkDir($workDir);
        }
    }

    private function createWorkDir(string $workDir): void
    {
        if (file_exists($workDir)) {
            if (is_dir($workDir)) {
                $message = sprintf("Directory '%s' (working directory for git subsplit) already exists", $workDir);
                $this->symfonyStyle->warning($message);
            } else {
                $this->throwNotADirectoryException($workDir);
            }
        } else {
            FileSystem::createDir($workDir);
        }
    }

    private function ensureWorkDir(string $workDir): void
    {
        if (! file_exists($workDir)) {
            $this->throwDirectoryNotFoundException($workDir);
        } elseif (! is_dir($workDir)) {
            $this->throwNotADirectoryException($workDir);
        }
    }

    private function throwDirectoryNotFoundException(string $name): void
    {
        $message = sprintf("The directory '%s' does not exist", $name);
        throw new DirectoryNotFoundException($message);
    }

    private function throwNotADirectoryException(string $name): void
    {
        $message = sprintf("The file '%s' exists but it is not a directory", $name);
        throw new DirectoryNotFoundException($message);
    }

    private function say($message): void
    {
        if (! $this->symfonyStyle->isQuiet()) {
            $this->symfonyStyle->writeln($message);
        }
    }
}
