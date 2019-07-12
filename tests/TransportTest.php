<?php

namespace HeadCouch;

use PHPUnit\Framework\TestCase;

class TransportTest extends TestCase
{
    public function testAttributes()
    {
        $attributes = array(
            'connectTimeout',
            'data',
            'error',
            'headers',
            'host',
            'httpCode',
            'httpInfo',
            'method',
            'password',
            'port',
            'response',
            'responseHeaders',
            'sslVerifyPeer',
            'timeout',
            'url',
            'userAgent',
            'username',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Transport::class);
        }
    }
}