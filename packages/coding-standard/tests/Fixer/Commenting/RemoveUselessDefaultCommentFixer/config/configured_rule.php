<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(RemoveUselessDefaultCommentFixer::class);
};
