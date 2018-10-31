<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderFixer\Wrong;

class SomeClass
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call1()
    {
    }

    private function call2()
    {
    }
}
