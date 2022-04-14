<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(LineLengthFixer::class, [
        LineLengthFixer::LINE_LENGTH => 100,
        LineLengthFixer::BREAK_LONG_LINES => true,
        LineLengthFixer::INLINE_SHORT_LINES => false,
    ]);
};
