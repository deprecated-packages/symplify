<?php
declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Fixture\Rector\Configurable;

use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\RectorInterface;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter\Source\SomeValueObjectWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class WithPHPStanTypeObject implements RectorInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ADDED_ARGUMENTS = 'added_arguments';

    public function getRuleDefinition(): RuleDefinition
    {
        $objectType = new ObjectType('SomeObject');

        $exampleConfiguration = [
            new SomeValueObjectWrapper($objectType),
        ];

        return new RuleDefinition('Some change', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
before
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
after
CODE_SAMPLE
                , $exampleConfiguration
            )
        ]);
    }
}
