<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Fixture;

use Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Source\OnlyDateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class SkipDateTimeClass extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => OnlyDateTime::class,
                'constraints' => new Valid(),
            ]
        );
    }
}
