<?php

declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class TranslatableTest extends TestCase
{

    public function testGetMethod()
    {
        $category = new Category('Some name', true);
        $this->assertSame('Some name', $category->getName());
    }


    public function testIsMethod()
    {
        $category = new Category('Some name', true);
        $this->assertTrue($category->isActive());
    }


    public function testHasMethod()
    {
        $tagDoctrine = new Tag('Doctrine');
        $tagNette = new Tag('Nette');

        $category = new Category('Some name', true);
        $category->addTag($tagDoctrine);
        $category->addTag($tagNette);

        $this->assertInstanceOf(ArrayCollection::class, $category->getTags());
        $this->assertSame(2, $category->getTags()->count());
        $this->assertTrue($category->hasTag($tagNette));
    }


    public function testShouldMethod()
    {
        $category = new Category('Some name', true);
        $category->setShouldRenderSubcategories(true);

        $this->assertTrue($category->shouldRenderSubcategories());
    }
}
