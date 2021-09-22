<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
/* /var/www/symplify/packages/phpstan-rules/packages/symfony/tests/Rules/NoTwigMissingMethodCallRule/Fixture/../Source/non_existing_method.twig */
class __TwigTemplate_eca546e05187cc5d39732a60db14f928b1999240eb6a251e25316e4f44dd4477 // extends \Twig\Template
{
    protected function doDisplay(array $context, array $blocks = [])
    {
        /** @var Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType $some_type */

        echo \strlen($some_type->nonExistingMethod(), "html", \null, \true);
        echo "\n";
    }

}
