<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipConditionalElseAssign
{
    /**
     * @var SmartFileInfo
     */
    private $value;

    public function run()
    {
        while ($i++ < 10) {
            if ($i > 100) {
            } else {
                $this->value = new SmartFileInfo('a.php');
            }
        }
    }
}
