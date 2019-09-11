<?php declare(strict_types=1);

namespace Symplify\Statie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\Exception\Configuration\ConfigurationException;

final class InitCommand extends Command
{
    /**
     * @var string
     */
    private const TWIG_TEMPLATING = 'twig';

    /**
     * @var string
     */
    private const LATTE_TEMPLATING = 'latte';

    /**
     * @var string
     */
    private const TEMPLATING_OPTION = 'templating';

    /**
     * @var string[]
     */
    private const TEMPLATING_ALLOWED_VALUES = [self::TWIG_TEMPLATING, self::LATTE_TEMPLATING];

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var string[]
     */
    private $templateDirectories = [
        'base' => __DIR__ . '/../../../templates/statie-website',
        'twig' => __DIR__ . '/../../../templates/statie-website-twig',
        'latte' => __DIR__ . '/../../../templates/statie-website-latte',
        // blog
        'blog' => __DIR__ . '/../../../templates/statie-blog',
        'blog-twig' => __DIR__ . '/../../../templates/statie-blog-twig',
        'blog-latte' => __DIR__ . '/../../../templates/statie-blog-latte',
        // travis-deploy
        'travis-deploy' => __DIR__ . '/../../../templates/travis-deploy',
    ];

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

        $templatingDescription = sprintf(
            'Template framework to use [%s]',
            implode('', self::TEMPLATING_ALLOWED_VALUES)
        );

        $this->addOption(
            self::TEMPLATING_OPTION,
            't',
            InputOption::VALUE_REQUIRED,
            $templatingDescription,
            self::TWIG_TEMPLATING
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $templating = $input->getOption(self::TEMPLATING_OPTION);
        $this->ensureTemplatingIsValid($templating);

        // generate website
        $this->copyTemplates('base');
        $this->copyTemplates($templating);

        $this->generateBlog($templating);
        $this->generateTravis();

        $this->symfonyStyle->success('Your new Statie is now generated');

        $this->symfonyStyle->note('Run "npm install" to get javascript dependencies');
        $this->symfonyStyle->note('Then run "gulp" to run website in your browser "localhost:8000"');

        return ShellCode::SUCCESS;
    }

    private function ensureTemplatingIsValid(string $name): void
    {
        if (in_array($name, self::TEMPLATING_ALLOWED_VALUES, true)) {
            return;
        }

        throw new ConfigurationException(sprintf(
            '--templating option %s is not allowed. Pick one of "%s"',
            $name,
            implode('", "', self::TEMPLATING_ALLOWED_VALUES)
        ));
    }

    private function copyTemplates(string $name): void
    {
        $this->ensureTemplateDirectoriesNameIsValid($name);
        $this->filesystem->mirror($this->templateDirectories[$name], $this->targetDirectory);
    }

    private function generateBlog(string $templating): void
    {
        $isBlog = $this->symfonyStyle->confirm('Do you want to blog?');
        if ($isBlog === false) {
            return;
        }

        $this->copyTemplates('blog');
        $this->copyTemplates('blog-' . $templating);
    }

    private function generateTravis(): void
    {
        $isTravis = $this->symfonyStyle->confirm('Do you want deploy via Travis to Github Pages?');
        if ($isTravis === false) {
            return;
        }

        $this->copyTemplates('travis-deploy');
    }

    private function ensureTemplateDirectoriesNameIsValid(string $name): void
    {
        if (isset($this->templateDirectories[$name])) {
            return;
        }

        throw new ConfigurationException(sprintf(
            'Template group "%s" was not found. Pick one of "%s"',
            $name,
            implode('", "', array_keys($this->templateDirectories))
        ));
    }
}
