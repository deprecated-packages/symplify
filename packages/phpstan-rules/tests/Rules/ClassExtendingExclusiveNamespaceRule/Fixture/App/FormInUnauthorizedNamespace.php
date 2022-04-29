<?php

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App;

use Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Source\Symfony\Component\Form\FormTypeInterface;

class FormInUnauthorizedNamespace implements FormTypeInterface
{
}
