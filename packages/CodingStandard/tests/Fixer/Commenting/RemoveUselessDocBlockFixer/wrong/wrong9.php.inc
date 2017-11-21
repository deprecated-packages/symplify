<?php

namespace SomeNamespace;

use Illuminate\Http\Request;

class SomeClass
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request
     */
    public function someMethod(Request $request): Request
    {
    }
}
