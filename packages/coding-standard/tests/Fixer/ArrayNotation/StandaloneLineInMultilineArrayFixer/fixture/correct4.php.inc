<?php

declare(strict_types=1);

class SomeClass
{
    public function getErrors()
    {
        $error = [
            'line' => $this->wrapMessageToStyle((string) $error->getLine(), $error->isFixable()),
            'message' => $this->wrapMessageToStyle($message, $error->isFixable()),
        ];
    }
}
