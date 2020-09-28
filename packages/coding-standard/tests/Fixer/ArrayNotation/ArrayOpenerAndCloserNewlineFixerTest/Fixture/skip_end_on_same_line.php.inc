<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\Fixture;

final class SkipEndOnSameLine
{
    /**
     * @Route(path="/{type}/{param}", name="two_params")
     */
    public function __invoke(string $type, string $param): Response
    {
        return $this->render('two_params.twig', [
            'type' => $type,
            'param' => $param,
        ]);
    }

    public function go()
    {
        return [['test', 1], ['test', 2], ['foo', 1], ['foo', 2]];
    }
}
