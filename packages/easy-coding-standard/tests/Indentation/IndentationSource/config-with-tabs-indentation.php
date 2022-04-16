<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $ecsConfig): void {
    $services = $ecsConfig->services();
    $services->set(IndentationTypeFixer::class)
        ->public();

    $ecsConfig->indentation(Option::INDENTATION_TAB);
};
