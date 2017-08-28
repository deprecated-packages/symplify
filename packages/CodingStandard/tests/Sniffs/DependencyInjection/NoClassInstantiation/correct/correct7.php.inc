<?php declare(strict_types=1);

final class PropertyWrapper
{
    public static function create(File $file): self
    {
        return new self($file);
    }

    public function getDocBlock()
    {
    }
}
