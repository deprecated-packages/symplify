<?php

class SomeController
{
    public function renderAction()
    {
        new JsonResponse([
            'status' => 'ok'
        ]);
    }
}
