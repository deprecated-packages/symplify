<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;

/**
 * class SomeClass
 */
class SomeClass1
{
}

/**
 * Class SomeClass
 */
class SomeClass2
{
}

/**
 * class SomeClass
 */
class SomeClass3
{
}

// class SomeClass
class SomeClass4
{
}

// class        SomeClass
class SomeClass5
{
}
?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\Fixture;


class SomeClass1
{
}


class SomeClass2
{
}


class SomeClass3
{
}


class SomeClass4
{
}


class SomeClass5
{
}
?>
