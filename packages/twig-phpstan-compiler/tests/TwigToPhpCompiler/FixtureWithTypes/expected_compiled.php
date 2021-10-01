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
/* %stests/TwigToPhpCompiler/FixtureWithTypes/input_file.twig */
class __TwigTemplate_%s extends \Twig\Template
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
        /** @var string $value */
        $macros = $this->macros;
        // line 1
        echo \twig_escape_filter($this->env, $value, "html", \null, \true);
        echo "\n";
    }
}
