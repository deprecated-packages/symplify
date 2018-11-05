<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Style;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class SymfonyStyleFactory
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    public function __construct()
    {
        $this->privatesCaller = new PrivatesCaller();
    }

    public function create(): SymfonyStyle
    {
        $input = new ArgvInput();
        $output = new ConsoleOutput();

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', $input, $output);

        // --debug is called
        if ($input->hasParameterOption('--debug')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        return new SymfonyStyle($input, $output);
    }
}
