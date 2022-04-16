<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(EncodingFixer::class);
    $ecsConfig->rule(FullOpeningTagFixer::class);
};
