<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations;

use Doctrine\DBAL\Migrations\OutputWriter as DoctrineOutputWriter;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OutputWriter extends DoctrineOutputWriter
{

    /**
     * @var OutputInterface
     */
    private $consoleOutput;


    public function setConsoleOutput(OutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }


    /**
     * @param string $message
     */
    public function write($message)
    {
        $this->getConsoleOutput()->writeln($message);
    }


    private function getConsoleOutput() : OutputInterface
    {
        if ($this->consoleOutput === null) {
            $this->consoleOutput = new ConsoleOutput;
        }
        return $this->consoleOutput;
    }
}
