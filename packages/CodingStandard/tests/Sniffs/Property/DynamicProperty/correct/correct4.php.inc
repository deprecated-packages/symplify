<?php

trait Payload
{
    protected $payload = [];

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
}

class SomeClass
{
    use Payload;
}
