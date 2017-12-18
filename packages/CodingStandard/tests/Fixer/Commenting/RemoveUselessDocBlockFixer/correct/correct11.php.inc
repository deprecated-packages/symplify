<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * Normalizes the value before any other normalization is applied.
     *
     * @return $value The normalized array value
     */
    protected function preNormalize($value)
    {
        return $value;
    }

}
