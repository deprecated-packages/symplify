<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Files\LineLengthSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(LineLengthSniff::class);
};
