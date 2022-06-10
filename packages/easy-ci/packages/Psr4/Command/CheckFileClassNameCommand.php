<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\SmartFileSystem\SmartFileInfo;

final class CheckFileClassNameCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private PhpClassLoader $phpClassLoader
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('check-file-class-name');
        $this->setDescription('Check if short file name is same as class name');

        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string[] $sources */
        $sources = (array) $input->getArgument(Option::SOURCES);
        $classesToFiles = $this->phpClassLoader->load($sources);

        $missMatchingClassNamesByFiles = [];
        foreach ($classesToFiles as $class => $file) {
            $fileBasename = pathinfo($file, PATHINFO_FILENAME);
            $shortClassName = Strings::after($class, '\\', -1);

            if ($shortClassName === $fileBasename) {
                continue;
            }

            $missMatchingClassNamesByFiles[$file] = $class;
        }

        if ($missMatchingClassNamesByFiles === []) {
            $this->symfonyStyle->success('All classes match their short file name');
            return self::SUCCESS;
        }

        foreach ($missMatchingClassNamesByFiles as $file => $class) {
            $fileInfo = new SmartFileInfo($file);
            $message = sprintf(
                'Check "%s" file to match class name "%s"',
                $fileInfo->getRelativeFilePathFromCwd(),
                $class
            );

            $this->symfonyStyle->warning($message);
        }

        return self::FAILURE;
    }
}
