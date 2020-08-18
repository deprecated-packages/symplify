<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
 */
class SomeEntity
{
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={
 * @ORM\Index(name="user_id", columns={"another_id"})
 * })
 */
class SomeEntity
{
}

?>
