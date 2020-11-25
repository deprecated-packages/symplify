<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity
 */
class RouteVisit implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $route;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $controller;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $routeHash;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $visitCount;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $method;

    public function __construct(string $route, string $controller, string $method, string $routeHash)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->routeHash = $routeHash;
        $this->visitCount = 1;
        $this->method = $method;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function increaseVisitCount(): void
    {
        ++$this->visitCount;
    }

    public function getVisitCount(): int
    {
        return $this->visitCount;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRouteHash(): string
    {
        return $this->routeHash;
    }
}
