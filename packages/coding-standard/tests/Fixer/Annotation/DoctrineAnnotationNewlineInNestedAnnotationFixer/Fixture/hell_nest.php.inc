<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="...", indexes={@ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"...", "..."}), @ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"..."}), @ORM\Index(name="...", columns={"..."})})
 * @ORM\Entity()
 */
class HellNest
{
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="...", indexes={
 * @ORM\Index(name="...", columns={"..."}),
 * @ORM\Index(name="...", columns={"...", "..."}),
 * @ORM\Index(name="...", columns={"..."}),
 * @ORM\Index(name="...", columns={"..."}),
 * @ORM\Index(name="...", columns={"..."}),
 * @ORM\Index(name="...", columns={"..."})
 * })
 * @ORM\Entity()
 */
class HellNest
{
}

?>
