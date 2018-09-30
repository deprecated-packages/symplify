<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symplify\PackageBuilder\Parameter\ParameterTypoProofreader;

final class ParameterTypoProofreaderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterTypoProofreader
     */
    private $parameterTypoProofreader;

    public function __construct(ParameterTypoProofreader $parameterTypoProofreader)
    {
        $this->parameterTypoProofreader = $parameterTypoProofreader;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'guideParameters',
            KernelEvents::REQUEST => 'guideParameters',
        ];
    }

    public function guideParameters(): void
    {
        $this->parameterTypoProofreader->process();
    }
}
