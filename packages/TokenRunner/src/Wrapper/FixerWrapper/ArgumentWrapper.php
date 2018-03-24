<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

final class ArgumentWrapper extends AbstractVariableWrapper
{
    public function changeName(string $newName): void
    {
        $this->changeNameWithTokenType($newName, T_VARIABLE);
    }

    protected function getNamePosition(): ?int
    {
        return $this->index;
    }
}
