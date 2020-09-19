<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Compiler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Compiler\Composer\ComposerJsonManipulator;
use Symplify\MonorepoBuilder\Compiler\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PackageBuilder\Process\ProcessRunner;

/**
 * Inspired by @see https://github.com/phpstan/phpstan-src/blob/f939d23155627b5c2ec6eef36d976dddea22c0c5/compiler/src/Console/CompileCommand.php
 */
final class CompileCommand extends Command
{
    /**
     * @var string
     */
    public const NAME = 'monorepo-builder:compile';

    /**
     * @var string
     */
    private $dataDir;

    /**
     * @var string
     */
    private $buildDir;

    /**
     * @var ComposerJsonManipulator
     */
    private $composerJsonManipulator;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(
        ParameterProvider $parameterProvider,
        SymfonyStyle $symfonyStyle,
        ComposerJsonManipulator $composerJsonManipulator,
        ProcessRunner $processRunner
    ) {
        parent::__construct();

        $this->dataDir = $parameterProvider->provideStringParameter(Option::DATA_DIR);
        $this->buildDir = $parameterProvider->provideStringParameter(Option::BUILD_DIR);

        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonManipulator = $composerJsonManipulator;
        $this->processRunner = $processRunner;
    }

    protected function configure(): void
    {
        $this->setName(self::NAME);
        $this->setDescription('Compile prefixed monorepo-builder.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $composerJsonFilePath */
        $composerJsonFilePath = realpath($this->buildDir . '/composer.json');

        $titleMessage = sprintf('1. Loading and updating "%s"', realpath($composerJsonFilePath));
        $this->symfonyStyle->title($titleMessage);

        $this->composerJsonManipulator->fixComposerJson($composerJsonFilePath);

        $this->symfonyStyle->title('2. Running "composer update" for new config');
        // @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/52

        $this->processRunner->createAndRun([
            'composer',
            'update',
            '--no-dev',
            '--prefer-dist',
            '--no-interaction',
            '--classmap-authoritative',
            '--ansi',
        ], $this->buildDir, $output);

        $this->symfonyStyle->title('3. Packing and prefixing monorepo-builder.phar with Box and PHP Scoper');
        // parallel prevention is just for single less-buggy process

        $this->processRunner->createAndRun(
            ['php', 'box.phar', 'compile', '--no-parallel', '--ansi'],
            $this->dataDir,
            $output
        );

        $this->symfonyStyle->title('4. Restoring original "composer.json" content');
        $this->composerJsonManipulator->restore();
        $this->symfonyStyle->note('You still need to run "composer update" to install those dependencies');

        $this->symfonyStyle->success('monorepo-builder.phar was generated');

        // success
        return 0;
    }
}
