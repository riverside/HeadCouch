<?php

namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'db',
            'document',
            'transport',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Document::class);
        }
    }
}