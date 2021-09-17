<?php

declare(strict_types=1);

use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render(): void
    {
        $this->template->render(__DIR__ . '/some_control.latte', [
            'someVariable' => new SomeType(),
            'dateTime' => new \Nette\Utils\DateTime('now')
        ]);
    }
}
