<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\NewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_dokumenty", indexes={@ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"..."})})
 */
class SomeEntity
{
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\NewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="a_dokumenty", indexes={
 * @ORM\Index(name="...", columns={"..."}),
 * @ORM\Index(name="...", columns={"..."})
 * })
 */
class SomeEntity
{
}

?>
