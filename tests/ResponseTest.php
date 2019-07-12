<?php

namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'response',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Response::class);
        }
    }
}