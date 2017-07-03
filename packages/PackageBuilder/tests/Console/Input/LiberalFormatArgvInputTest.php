<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Console\Input;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symplify\PackageBuilder\Console\Input\LiberalFormatArgvInput;

final class LiberalFormatArgvInputTest extends TestCase
{
    /**
     * @var LiberalFormatArgvInput
     */
    private $formatLiberalArgvInput;

    protected function setUp(): void
    {
        $inputDefinition = new InputDefinition([
            new InputOption('config', NULL, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED)
        ]);
        $this->formatLiberalArgvInput = new LiberalFormatArgvInput([], $inputDefinition);
    }

    public function test(): void
    {
        $this->formatLiberalArgvInput->setOption('config', ['one,two']);
        $this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOption('config'));
        $this->assertSame(['one', 'two'], $this->formatLiberalArgvInput->getOptions()['config']);
    }
}
