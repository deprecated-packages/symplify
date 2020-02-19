<?php

declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class InitCommand extends Command
{
    /**
     * @var string
     */
    private const BLOG = 'blog';

    /**
     * @var string
     */
    private const TWIG = 'twig';

    /**
     * @var string
     */
    private const BLOG_TWIG = 'blog-twig';

    /**
     * @var string
     */
    private const TRAVIS_DEPLOY = 'travis-deploy';

    /**
     * @var string
     */
    private const BASE = 'base';

    /**
     * @var string[]
     */
    private const TEMPLATE_DIRECTORIES = [
        self::BASE => __DIR__ . '/../../../templates/statie-website',
        self::TWIG => __DIR__ . '/../../../templates/statie-website-twig',
        // blog
        self::BLOG => __DIR__ . '/../../../templates/statie-blog',
        self::BLOG_TWIG => __DIR__ . '/../../../templates/statie-blog-twig',
        // travis-deploy
        self::TRAVIS_DEPLOY => __DIR__ . '/../../../templates/travis-deploy',
    ];

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(SymfonyStyle $symfonyStyle, Filesystem $filesystem)
    {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->filesystem = $filesystem;
        $this->targetDirectory = getcwd();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate a basic site and blog');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // generate website
        $this->copyTemplates(self::BASE);
        $this->copyTemplates(self::TWIG);

        $this->generateBlog();
        $this->generateTravis();

        $this->symfonyStyle->success('Your new Statie is now generated');

        $this->symfonyStyle->note('Run "npm install" to get javascript dependencies');
        $this->symfonyStyle->note('Then run "gulp" to run website in your browser "localhost:8000"');

        return ShellCode::SUCCESS;
    }

    private function copyTemplates(string $name): void
    {
        $this->filesystem->mirror(self::TEMPLATE_DIRECTORIES[$name], $this->targetDirectory);
    }

    private function generateBlog(): void
    {
        $isBlog = $this->symfonyStyle->confirm('Do you want to blog?');
        if (! $isBlog) {
            return;
        }

        $this->copyTemplates(self::BLOG);
        $this->copyTemplates(self::BLOG_TWIG);
    }

    private function generateTravis(): void
    {
        $isTravis = $this->symfonyStyle->confirm('Do you want deploy via Travis to Github Pages?');
        if (! $isTravis) {
            return;
        }

        $this->copyTemplates(self::TRAVIS_DEPLOY);
    }
}
