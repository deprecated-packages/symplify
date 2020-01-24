<?php

declare(strict_types=1);

class A
{
    public function __construct($orWhere)
    {
        $sql = 'UPDATE answer 
                SET score = score + __points__ * (
                        SELECT COUNT(aa.id) 
                        FROM answer_to_answer_definition aa 
                        WHERE answer.id = aa.pickX_answer_id AND ('.implode(' OR ', $orWhere).')
                ).';
    }
}