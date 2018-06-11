<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

final class Change
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var null|string
     */
    private $category;

    public function __construct(string $message, ?string $category, ?string $package)
    {
        $this->message = $message;
        $this->category = $category;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }
}
