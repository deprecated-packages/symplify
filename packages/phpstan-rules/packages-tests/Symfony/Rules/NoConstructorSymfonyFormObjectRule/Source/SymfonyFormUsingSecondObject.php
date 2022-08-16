<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule\Source;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHPStanRules\Tests\Symfony\Rules\NoConstructorSymfonyFormObjectRule\Fixture\ClassUsedInSymfonyFormButWithConstructor;

final class SymfonyFormUsingSecondObject extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassUsedInSymfonyFormButWithConstructor::class,
        ]);
    }
}
