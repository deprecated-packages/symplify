<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule\Fixture;

use SimpleXMLElement;

final class SkipPropertyExistsOnXml
{
    /**
     * @param null|SimpleXMLElement $element
     */
    public function run($element)
    {
        return property_exists($element, 'items');
    }

    /**
     * @param SimpleXMLElement $element
     */
    public function runExactly($element)
    {
        return property_exists($element, 'items');
    }
}
