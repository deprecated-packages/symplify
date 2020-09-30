<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class ManyMultiLineChain
{
    public function run($treeNodeEntity)
    {
        $childChildItem = $treeNodeEntity->getChildNodes()->get(0)->getChildNodes();
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\Fixture;

class ManyMultiLineChain
{
    public function run($treeNodeEntity)
    {
        $childChildItem = $treeNodeEntity->getChildNodes()
->get()
->getChildNodes();
    }
}

?>
