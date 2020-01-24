<?php

namespace Easybook\Plugins;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParserPlugin implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [];
    }
}
