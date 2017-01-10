<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\DI;

use Knp\DoctrineBehaviors\ORM\Loggable\LoggerCallable;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Nette\DI\Compiler;
use PHPUnit\Framework\TestCase;
use Zenify\DoctrineBehaviors\DI\AbstractBehaviorExtension;
use Zenify\DoctrineBehaviors\Tests\DI\AbstractBehaviorExtensionSource\SomeBehaviorExtension;

final class AbstractBehaviorExtensionTest extends TestCase
{

    /**
     * @var AbstractBehaviorExtension|SomeBehaviorExtension
     */
    private $abstractBehaviorsExtension;


    protected function setUp()
    {
        $this->abstractBehaviorsExtension = new SomeBehaviorExtension;
        $this->abstractBehaviorsExtension->setCompiler(new Compiler, 'someBehavior');
    }


    public function testGetClassAnalyzer()
    {
        $classAnalyzer = $this->abstractBehaviorsExtension->getClassAnalyzerPublic();
        $this->assertSame(ClassAnalyzer::class, $classAnalyzer->getClass());

        $sameClassAnalyzer = $this->abstractBehaviorsExtension->getClassAnalyzerPublic();
        $this->assertSame($classAnalyzer, $sameClassAnalyzer);
    }


    public function testBuildDefinitionWithNullValue()
    {
        $this->assertNull($this->abstractBehaviorsExtension->buildDefinitionFromCallablePublic(null));
    }


    public function testBuildDefinition()
    {
        $definition = $this->abstractBehaviorsExtension->buildDefinitionFromCallablePublic(LoggerCallable::class);

        $this->assertSame(LoggerCallable::class, $definition->getClass());
        $this->assertSame(LoggerCallable::class, $definition->getFactory()->getEntity());
    }
}
