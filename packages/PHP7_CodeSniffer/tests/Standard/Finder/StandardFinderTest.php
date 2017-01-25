<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Standard\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class StandardFinderTest extends TestCase
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    protected function setUp()
    {
        $this->standardFinder = new StandardFinder();
    }

    public function testGetStandards()
    {
        $this->assertCount(10, $this->standardFinder->getStandards());
    }

    public function testGetRulesetPathForStandardName()
    {
        $rulesetPath = $this->standardFinder->getRulesetPathForStandardName('PSR2');
        $this->assertStringMatchesFormat(
            '%ssrc/Standards/PSR2/ruleset.xml',
            $rulesetPath
        );
    }

    public function testGetRulesetPathsForStandardNames()
    {
        $rulesetPaths = $this->standardFinder->getRulesetPathsForStandardNames(['PSR2']);
        $this->assertStringMatchesFormat(
            '%ssrc/Standards/PSR2/ruleset.xml',
            $rulesetPaths['PSR2']
        );
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException
     */
    public function testGetRulesetPathForNonExistingStandardName()
    {
        $this->standardFinder->getRulesetPathForStandardName('non-existing');
    }
}
