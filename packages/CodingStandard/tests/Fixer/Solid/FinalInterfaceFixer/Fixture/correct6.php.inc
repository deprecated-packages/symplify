<?php

namespace SomeNamespace\AnonymousClass;

// from correct.php.inc
final class SomeClass implements SomeSpecialInterface
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}

// from correct2.php.inc
abstract class SomeAbstractClass implements SomeSpecialInterface
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}

// from correct4.php.inc

/**
 * @ORM\SomeAnnotation
 * @ORM\Entity
 * @ORM\SomeAnotherAnnotation
 */
class SomeEntity implements SomeInterface
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}

// from correct4.php.inc
class AnotherEntity
{
    public function createAnonymousClass()
    {
        return new class
        {
        };
    }

    public function createAnonymousClassImplementsInterface(): SomeInterface
    {
        return new class implements SomeInterface
        {
        };
    }
}
