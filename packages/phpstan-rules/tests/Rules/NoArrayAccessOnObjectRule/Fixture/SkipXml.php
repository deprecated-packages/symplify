<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule\Fixture;

use SimpleXMLElement;

final class SkipXml
{
    public function run(SimpleXMLElement $values)
    {
        return $values['key'];
    }
}
