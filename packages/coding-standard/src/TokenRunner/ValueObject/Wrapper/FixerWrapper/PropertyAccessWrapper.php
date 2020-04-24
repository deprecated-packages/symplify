<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

final class PropertyAccessWrapper extends AbstractVariableWrapper
{
    public function changeName(string $newName): void
    {
        $this->changeNameWithTokenType($newName, T_STRING);
    }

    protected function getNamePosition(): ?int
    {
        return $this->tokens->getNextMeaningfulToken($this->index + 1);
    }
}
