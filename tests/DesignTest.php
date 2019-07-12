<?php

namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class DesignTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'db',
            'document',
            'transport',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Design::class);
        }
    }
}