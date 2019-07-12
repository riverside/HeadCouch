<?php

namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'db',
            'transport',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Database::class);
        }
    }
}