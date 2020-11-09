<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\Fixture;

class SingleElementWithoutList
{
    public function __invoke(string $param)
    {
        return $this->render('one_param.twig', ['param' => $param]);
    }

    public function getArguments()
    {
        return [1, 2];
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\Fixture;

class SingleElementWithoutList
{
    public function __invoke(string $param)
    {
        return $this->render('one_param.twig', [
'param' => $param
]);
    }

    public function getArguments()
    {
        return [1, 2];
    }
}

?>
