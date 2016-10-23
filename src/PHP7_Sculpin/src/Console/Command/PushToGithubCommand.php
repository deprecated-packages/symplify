<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_Sculpin\Github\GihubPublishingProcess;
use Throwable;

final class PushToGithubCommand extends Command
{
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var GihubPublishingProcess
     */
    private $gihubPublishingProcess;

    public function __construct(string $outputDirectory, GihubPublishingProcess $gihubPublishingProcess)
    {
        $this->outputDirectory = $outputDirectory;
        $this->gihubPublishingProcess = $gihubPublishingProcess;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('push-to-github');
        $this->setDescription('Push generated site to Github pages.');
        $this->addArgument(
            'repository-slug',
            InputArgument::REQUIRED,
            'Repository slug, e.g. "TomasVotruba/tomasvotruba.cz".'
        );
        $this->addOption('token', null, InputOption::VALUE_REQUIRED, 'Github token.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->ensureInputIsValid($input);

            $this->gihubPublishingProcess->setupTravisIdentityToGit();

            $githubRepository = $this->createGithubRepositoryUrlWithToken(
                $input->getOption('token'),
                $input->getArgument('repository-slug')
            );

            $this->gihubPublishingProcess->pushDirectoryContentToRepository($this->outputDirectory, $githubRepository);

            $output->writeln('<info>Website was successfully pushed to Github pages.</info>');

            return 0;
        } catch (Throwable $throwable) {
            $output->writeln(
                sprintf('<error>%s</error>', $throwable->getMessage())
            );

            return 1;
        }
    }

    private function ensureInputIsValid(InputInterface $input)
    {
        $this->ensureTokenOptionIsSet((string) $input->getOption('token'));
        $this->ensureGithubRepositorySlugIsValid($input->getArgument('repository-slug'));
    }

    private function ensureTokenOptionIsSet(string $token)
    {
        if ($token === '') {
            throw new \Exception('Set token value via "--token=<GITHUB_TOKEN>" option.');
        }
    }

    private function ensureGithubRepositorySlugIsValid(string $repositorySlug)
    {
        $repositoryUrl = 'https://github.com/' . $repositorySlug;
        if (! $this->doesUrlExist($repositoryUrl)) {
            throw new \Exception(sprintf(
                'Repository "%s" is not accessible. Try fixing the "%s" slug.',
                $repositoryUrl,
                $repositorySlug
            ));
        }
    }

    private function doesUrlExist(string $url) : bool
    {
        $fileHeaders = @get_headers($url);
        if (! $fileHeaders || $fileHeaders[0] === 'HTTP/1.1 404 Not Found') {
            return false;
        }

        return true;
    }

    private function createGithubRepositoryUrlWithToken(string $token, string $repositorySlug) : string
    {
        return sprintf(
            'https://%s@github.com/%s.git',
            $token,
            $repositorySlug
        );
    }
}
