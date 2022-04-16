<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(StrictComparisonFixer::class);
    $ecsConfig->rule(IsNullFixer::class);
    $ecsConfig->rule(StrictParamFixer::class);
    $ecsConfig->rule(DeclareStrictTypesFixer::class);
};
