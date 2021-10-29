<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        /** @var Nette\Localization\Translator $netteLocalizationTranslatorFilter */
        /** line in latte file: 1 */
        echo \Latte\Runtime\Filters::escapeHtmlText($netteLocalizationTranslatorFilter->translate($var));
        echo "\n";
        /** line in latte file: 2 */
        echo \Latte\Runtime\Filters::escapeHtmlText($netteLocalizationTranslatorFilter->translate($var));
        echo "\n";
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        /** @var Nette\Localization\Translator $netteLocalizationTranslatorFilter */
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
