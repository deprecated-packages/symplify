<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Git;

use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class ExpectedAliasResolver
{
    /**
     * @var VersionUtils
     */
    private $versionUtils;

    public function __construct(VersionUtils $versionUtils)
    {
        $this->versionUtils = $versionUtils;
    }

    public function resolve(): string
    {
        $process = new Process(['git', 'describe', '--abbrev=0', '--tags']);
        $process->run();

        $output = $process->getOutput();

        return $this->versionUtils->getNextAliasFormat($output);
    }
}
