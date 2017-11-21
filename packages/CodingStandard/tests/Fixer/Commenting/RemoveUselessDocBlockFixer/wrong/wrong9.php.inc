<?php

namespace SomeNamespace;

use Illuminate\Http\Request;

class SomeClass
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request
     */
    public function __construct(Request $request): Request
    {
    }
}
