<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Fixture;

use Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Source\CollectionEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class CollectionPropertyFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => CollectionEntity::class,
                'constraints' => new Valid(),
            ]
        );
    }
}
