<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SemicolonSpacingSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->lineEnding("\r\n");
    $ecsConfig->rule(SemicolonSpacingSniff::class);
};
