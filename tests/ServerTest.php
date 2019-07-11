<?php
namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'transport',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Server::class);
        }
    }
}