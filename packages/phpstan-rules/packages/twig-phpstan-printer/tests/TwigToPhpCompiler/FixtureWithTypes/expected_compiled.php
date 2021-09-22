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
/* /var/www/symplify/packages/phpstan-rules/packages/twig-phpstan-printer/tests/TwigToPhpCompiler/FixtureWithTypes/input_file.twig */
class __TwigTemplate_94c8e719ef5da7bdc855cb31d165f0517b94c375d527ef2679694beaa06804c1 extends \Twig\Template
{
    private $source;
    private $macros = [];
    public function __construct(\Twig\Environment $env)
    {
        parent::__construct($env);
        $this->source = $this->getSourceContext();
        $this->parent = \false;
        $this->blocks = [];
    }
    protected function doDisplay(array $context, array $blocks = [])
    {
        extract($context);
        /** @var string $someName */
        $macros = $this->macros;
        // line 1
        echo \strlen($context["value"] ?? \null, "html", \null, \true);
        echo "\n";
    }
    public function getTemplateName()
    {
        return "/var/www/symplify/packages/phpstan-rules/packages/twig-phpstan-printer/tests/TwigToPhpCompiler/FixtureWithTypes/input_file.twig";
    }
    public function isTraitable()
    {
        return \false;
    }
    public function getDebugInfo()
    {
        return array(37 => 1);
    }
    public function getSourceContext()
    {
        return new \Twig\Source("", "/var/www/symplify/packages/phpstan-rules/packages/twig-phpstan-printer/tests/TwigToPhpCompiler/FixtureWithTypes/input_file.twig", "");
    }
}
