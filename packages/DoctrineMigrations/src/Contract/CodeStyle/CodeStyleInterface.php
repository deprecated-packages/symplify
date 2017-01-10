<?php

declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Contract\CodeStyle;

interface CodeStyleInterface
{

    public function applyForFile(string $file);
}
