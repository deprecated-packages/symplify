<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\Fixture;

use SimpleXMLElement;

final class SkipXmlElementForeach
{
    public function run(SimpleXMLElement $simpleXMLElement)
    {
        foreach ($simpleXMLElement->children() as $name => $childElement) {
            return isset($childElement['some']);
        }
    }
}
