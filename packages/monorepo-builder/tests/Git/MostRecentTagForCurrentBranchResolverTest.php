<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Git;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\Git\MostRecentTagForCurrentBranchResolver;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunnerInterface;

final class MostRecentTagForCurrentBranchResolverTest extends TestCase
{
    private ProcessRunner|MockObject $processRunner;

    protected function setUp(): void
    {
        $this->processRunner = $this->createMock(ProcessRunnerInterface::class);
    }

    public function testReturnMostRecentTagStringWithoutNewlineCharacters(): void
    {
        $this->processRunner
            ->method('run')
            ->with(MostRecentTagForCurrentBranchResolver::COMMAND, 'git-directory')
            ->willReturn("\r1.0.0\r\n")
        ;

        $mostRecentTagForCurrentBranchResolver = new MostRecentTagForCurrentBranchResolver($this->processRunner);

        $result = $mostRecentTagForCurrentBranchResolver->resolve('git-directory');

        self::assertSame('1.0.0', $result);
    }

    public function testReturnZeroWhenEmptyStringIsReturnedFromProcessRun(): void
    {
        $this->processRunner
            ->method('run')
            ->with(MostRecentTagForCurrentBranchResolver::COMMAND, 'git-directory')
            ->willReturn('')
        ;

        $mostRecentTagForCurrentBranchResolver = new MostRecentTagForCurrentBranchResolver($this->processRunner);

        $result = $mostRecentTagForCurrentBranchResolver->resolve('git-directory');

        self::assertNull($result);
    }

    private function createMostRecentTagForCurrentBranchResolver(): MostRecentTagForCurrentBranchResolver
    {
        return new MostRecentTagForCurrentBranchResolver($this->processRunner);
    }
}
