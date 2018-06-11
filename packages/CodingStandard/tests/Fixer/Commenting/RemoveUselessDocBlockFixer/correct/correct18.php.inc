<?php declare(strict_types=1);

class CustomAnnotationTest
{
    /**
     * @customAnnotation
     */
    public function customAnnotation(): void
    {
    }

    /**
     * @customAnnotation(arg)
     */
    public function customAnnotationWithArgument(): void
    {
    }
}
