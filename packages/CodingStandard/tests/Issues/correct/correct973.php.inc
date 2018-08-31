<?php

namespace Easybook\Plugins;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParserPlugin
{
    public function someMethod(
        $value // somment
    ) {
        $array = [
            'values' // some comment
        ];

        $array = [
            'values' /* some comment */
        ];

        return [
            'values' # some comment
        ];
    }
}
