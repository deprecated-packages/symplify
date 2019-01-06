<?php

use Symplify\CodingStandard\Tests\Fixer\Naming\CatchExceptionNameMatchingTypeFixer\Source\DangerousException;

try {
    $value = 1;
} catch (DangerousException $dangerouslyException) {
    throw $dangerouslyException;
}

?>
-----
<?php

use Symplify\CodingStandard\Tests\Fixer\Naming\CatchExceptionNameMatchingTypeFixer\Source\DangerousException;

try {
    $value = 1;
} catch (DangerousException $dangerousException) {
    throw $dangerousException;
}

?>
