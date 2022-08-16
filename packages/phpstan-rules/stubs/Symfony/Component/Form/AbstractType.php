<?php

declare(strict_types=1);

namespace Symfony\Component\Form;

if (class_exists(\Symfony\Component\Form\AbstractType::class)) {
    return;
}

abstract class AbstractType
{
}
