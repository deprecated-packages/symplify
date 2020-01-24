<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderFixer\Wrong;

class SomeClassToBeOrdered
{
    public function run()
    {
        $this->call1();
        $this->call2();
    }

    private function call2()
    {
    }

    private function call1()
    {
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderFixer\Wrong;

class SomeClassToBeOrdered
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

?>
