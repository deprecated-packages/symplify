<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Github;

use Symfony\Component\Process\Process;
use Symplify\PHP7_Sculpin\Utils\FilesystemChecker;

final class GihubPublishingProcess
{
    public function setupTravisIdentityToGit()
    {
        if (getenv('TRAVIS')) {
            $this->runScript('git config --global user.email "travis@travis-ci.org"');
            $this->runScript('git config --global user.name "Travis"');
        }
    }

    public function pushDirectoryContentToRepository(string $outputDirectory, string $githubRepository)
    {
        FilesystemChecker::ensureDirectoryExists($outputDirectory);

        $this->runScript(
            'git init && git add . && git commit -m "Regenerate output"',
            $outputDirectory
        );

        if (getenv('TRAVIS')) {
            $this->runScript(sprintf(
                'git push --force --quiet "${%s}" master:gh-pages > /dev/null 2>&1',
                $githubRepository
            ), $outputDirectory);
        }
    }

    private function runScript(string $script, string $workingDirectory = null)
    {
        $process = new Process($script);
        if ($workingDirectory) {
            $process->setWorkingDirectory($workingDirectory);
        }

        $process->run();
    }
}
