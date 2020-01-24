<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderFixer\Wrong;

class SomeClass2
{
    public function run()
    {
        $this->call3();
    }

    private function call2()
    {
    }

    private function call1()
    {
    }

    private function call3()
    {
        $this->call1();
        $this->call2();
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderFixer\Wrong;

class SomeClass2
{
    public function run()
    {
        $this->call3();
    }

    private function call3()
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

?>
