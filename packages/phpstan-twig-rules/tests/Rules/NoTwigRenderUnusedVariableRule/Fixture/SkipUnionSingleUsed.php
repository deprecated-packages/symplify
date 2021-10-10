<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigRenderUnusedVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SkipUnionSingleUsed extends AbstractController
{
    public function __invoke($random): Response
    {
        $template = $random ? __DIR__ . '/../Source/another_blank_template.twig' : __DIR__ . '/../Source/used_variable_template.twig';

        return $this->render($template, [
            'used_variable' => 'some_value'
        ]);
    }
}
