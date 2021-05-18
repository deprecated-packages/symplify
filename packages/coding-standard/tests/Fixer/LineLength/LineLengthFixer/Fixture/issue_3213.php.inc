<?php

class SomeClass
{
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];
}

?>
-----
<?php

class SomeClass
{
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];
}

?>
