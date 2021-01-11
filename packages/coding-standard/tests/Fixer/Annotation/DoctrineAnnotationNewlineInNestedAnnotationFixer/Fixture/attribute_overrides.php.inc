<?php

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\AttributeOverrides(@ORM\AttributeOverride(name="...", column=@ORM\Column(type="...")))
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
 * @ORM\AttributeOverrides(
 * @ORM\AttributeOverride(name="...", column=
 * @ORM\Column(type="..."
 * ))
 * )
 */
class SomeEntity
{
}

?>
