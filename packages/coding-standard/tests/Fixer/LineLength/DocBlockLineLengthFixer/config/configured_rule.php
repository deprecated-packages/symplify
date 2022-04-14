<?php

declare(strict_types=1);

use Symplify\CodingStandard\Fixer\LineLength\DocBlockLineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->ruleWithConfiguration(DocBlockLineLengthFixer::class, [
        DocBlockLineLengthFixer::LINE_LENGTH => 40,
    ]);
};
