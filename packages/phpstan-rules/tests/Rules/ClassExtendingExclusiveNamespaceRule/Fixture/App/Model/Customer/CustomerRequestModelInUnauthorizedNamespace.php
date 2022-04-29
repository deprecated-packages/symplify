<?php

namespace Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Fixture\App\Model\Customer;

use Symplify\PHPStanRules\Tests\Rules\ClassExtendingExclusiveNamespaceRule\Source\App\Component\ParamConverter\RequestInterface;

class CustomerRequestModelInUnauthorizedNamespace implements RequestInterface
{

}
