<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

$friends = [1 => 'Peter', 2 => 'Paul',];

$friends = array(1 => 'Peter', 2 => 'Paul',);

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Fixture;

$friends = [
    1 => 'Peter',
    2 => 'Paul',
];

$friends = array(
    1 => 'Peter',
    2 => 'Paul',
);

?>
