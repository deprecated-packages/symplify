<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInLoopRule\Fixture;

final class SkipConditionalAssign
{
    /**
     * @var SmartFileInfo
     */
    private $value;

    public function run()
    {
        while ($i++ < 10) {
            if ($i > 100) {
                $this->value = new SmartFileInfo('a.php');
            }
        }
    }
}
