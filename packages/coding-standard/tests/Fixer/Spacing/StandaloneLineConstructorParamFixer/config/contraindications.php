<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\BracesFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    // this rule has higher priority over LineLenghtFixer, so value should be newlined
    $ecsConfig->rule(StandaloneLineConstructorParamFixer::class);
    $ecsConfig->rule(BracesFixer::class);
    $ecsConfig->rule(LineLengthFixer::class);
};
