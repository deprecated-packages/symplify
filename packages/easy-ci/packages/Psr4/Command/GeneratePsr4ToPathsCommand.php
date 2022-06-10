<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCI\Psr4\Configuration\Psr4SwitcherConfiguration;
use Symplify\EasyCI\Psr4\Json\JsonAutoloadPrinter;
use Symplify\EasyCI\Psr4\Psr4Filter;
use Symplify\EasyCI\Psr4\RobotLoader\PhpClassLoader;
use Symplify\EasyCI\Psr4\ValueObject\Option;
use Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPath;
use Symplify\EasyCI\Psr4\ValueObjectFactory\Psr4NamespaceToPathFactory;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;

final class GeneratePsr4ToPathsCommand extends AbstractSymplifyCommand
{
    public function __construct(
        private Psr4SwitcherConfiguration $psr4SwitcherConfiguration,
        private PhpClassLoader $phpClassLoader,
        private Psr4NamespaceToPathFactory $psr4NamespaceToPathFactory,
        private Psr4Filter $psr4Filter,
        private JsonAutoloadPrinter $jsonAutoloadPrinter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('generate-psr4-to-paths');
        $this->setDescription('Check if application is PSR-4 ready');

        $this->addArgument(Option::SOURCES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path to source');
        $this->addOption(Option::COMPOSER_JSON, null, InputOption::VALUE_REQUIRED, 'Path to composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->psr4SwitcherConfiguration->loadFromInput($input);

        $classesToFiles = $this->phpClassLoader->load($this->psr4SwitcherConfiguration->getSource());

        $psr4NamespacesToPaths = [];
        $classesToFilesWithMissedCommonNamespace = [];
        foreach ($classesToFiles as $class => $file) {
            $psr4NamespaceToPath = $this->psr4NamespaceToPathFactory->createFromClassAndFile($class, $file);
            if (! $psr4NamespaceToPath instanceof Psr4NamespaceToPath) {
                $classesToFilesWithMissedCommonNamespace[$class] = $file;
                continue;
            }

            $psr4NamespacesToPaths[] = $psr4NamespaceToPath;
        }

        $psr4NamespaceToPaths = $this->psr4Filter->filter($psr4NamespacesToPaths);
        $jsonAutoloadContent = $this->jsonAutoloadPrinter->createJsonAutoloadContent($psr4NamespaceToPaths);
        $this->symfonyStyle->writeln($jsonAutoloadContent);

        $this->symfonyStyle->success('Done');

        foreach ($classesToFilesWithMissedCommonNamespace as $class => $file) {
            $message = sprintf('Class "%s" and file "%s" have no match in PSR-4 namespace', $class, $file);
            $this->symfonyStyle->warning($message);
        }

        return self::SUCCESS;
    }
}
