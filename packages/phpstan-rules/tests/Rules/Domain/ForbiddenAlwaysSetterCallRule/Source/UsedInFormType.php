<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class UsedInFormType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SomeObject::class,
        ]);
    }
}
