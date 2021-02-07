<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ServiceAndValueObjectHaveSameStartsRule\Fixture;

use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\PHPStanExtensions\Tests\Rule\ServiceAndValueObjectHaveSameStartsRule\Source\ConfigureValueObject;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (object $random): void {
    $random->set(ChangeMethodVisibilityRector::class)
        ->call('configure', [[
            ChangeMethodVisibilityRector::METHOD_VISIBILITIES => ValueObjectInliner::inline([
                new ConfigureValueObject(),
                new ConfigureValueObject(),
            ]),
        ]]);
};
