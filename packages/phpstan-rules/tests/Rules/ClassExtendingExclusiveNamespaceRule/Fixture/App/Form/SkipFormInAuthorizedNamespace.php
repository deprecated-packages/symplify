<?php

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App\Form;

use Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Source\Symfony\Component\Form\FormTypeInterface;

class SkipFormInAuthorizedNamespace implements FormTypeInterface
{
}
