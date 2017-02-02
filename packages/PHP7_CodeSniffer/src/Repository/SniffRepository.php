<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Repository;

use Gherkins\RegExpBuilderPHP\RegExp;
use Gherkins\RegExpBuilderPHP\RegExpBuilder;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class SniffRepository
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var string[][]
     */
    private $sniffClasses;

    public function __construct(SniffFinder $sniffFinder)
    {
        $this->sniffFinder = $sniffFinder;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        $this->init();

        $groups = array_keys($this->sniffClasses);
        return array_combine($groups, $groups);
    }

    private function init(): void
    {
        if (count($this->sniffClasses)) {
            return;
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();
        $regExp = $this->createGroupRegularExpression();

        foreach ($sniffClasses as $sniffClass) {
            $group = $regExp->exec($sniffClass)[0];
            $this->sniffClasses[$group][] = $sniffClass;
        }
    }

    private function createGroupRegularExpression(): RegExp
    {
        $builder = new RegExpBuilder();
        return $builder->anythingBut('\\') // after \\
            ->ahead($builder->getNew()->exactly(1)->of('\\Sniffs')) // before \\Sniffs
            ->getRegExp();
    }

    /**
     * @return string[]
     */
    public function getByGroup(string $group): array
    {
        $this->init();

        if (!isset($this->sniffClasses[$group])) {
            return [];
        }

        return $this->sniffClasses[$group];
    }
}
