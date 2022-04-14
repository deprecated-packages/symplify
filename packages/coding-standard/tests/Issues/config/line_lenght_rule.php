<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (\Symplify\EasyCodingStandard\Config\ECSConfig $ecsConfig): void {
    $ecsConfig->rule(LineLengthFixer::class);
};
