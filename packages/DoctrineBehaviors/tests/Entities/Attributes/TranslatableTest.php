<?php declare(strict_types=1);

namespace Zenify\DoctrineBehaviors\Tests\Entities\Source;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class TranslatableTest extends TestCase
{
    public function testGetMethod(): void
    {
        $category = new Category('Some name', true);
        $this->assertSame('Some name', $category->getName());
    }

    public function testIsMethod(): void
    {
        $category = new Category('Some name', true);
        $this->assertTrue($category->isActive());
    }

    public function testHasMethod(): void
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

    public function testShouldMethod(): void
    {
        $category = new Category('Some name', true);
        $category->setShouldRenderSubcategories(true);

        $this->assertTrue($category->shouldRenderSubcategories());
    }
}
