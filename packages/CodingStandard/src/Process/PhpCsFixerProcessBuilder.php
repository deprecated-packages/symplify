<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symplify\CodingStandard\Contract\Process\ProcessBuilderInterface;

final class PhpCsFixerProcessBuilder implements ProcessBuilderInterface
{
    /**
     * @var ProcessBuilder
     */
    private $builder;

    public function __construct(string $directory)
    {
        $this->builder = new ProcessBuilder;
        $this->builder->setPrefix('./vendor/bin/php-cs-fixer');
        $this->builder->add('fix');
        $this->builder->add($directory);
        $this->builder->add('--verbose');
        $this->builder->add('--allow-risky=yes');
    }

    public function getProcess() : Process
    {
        return $this->builder->getProcess();
    }

    public function setLevel(string $level) : void
    {
        $this->builder->add('--rules=@' . $level);
    }

    public function setRules(string $rules) : void
    {
        $this->builder->add('--rules=' . $rules);
    }

    public function enableDryRun()
    {
        $this->builder->add('--dry-run');
        $this->builder->add('--diff');
    }
}
