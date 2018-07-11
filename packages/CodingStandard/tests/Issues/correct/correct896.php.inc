<?php

class Foo
{
    public function render(AbstractFile $file, array $parameters): string
    {
        return $this->codeBlocksProtector->protectContentFromCallback($file->getContent(), function (string $content) use (
            $file,
            $parameters
        ) {
            return '...';
        });
    }
}
