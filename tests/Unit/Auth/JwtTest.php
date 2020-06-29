<?php

namespace AtlassianConnectLaravel\Tests\Unit\Auth;

use AtlassianConnectLaravel\Auth\Jwt;
use PHPUnit\Framework\TestCase;

class JwtTest extends TestCase
{
    //Jwt::create('http://localhost:8000', 'GET', 'issuer', 'secret');
    protected string $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOi'
                            . 'Jpc3N1ZXIiLCJpYXQiOjE1OTM0MzUzMzgsImV4cCI6MTU5MzUyM'
                            . 'TczOCwicXNoIjoiYzg4Y2FhZDE1YTFjMWE5MDBiOGFjMDhhYTk2ODZmN'
                            . 'GU4MTg0NTM5YmVhMWRlZGEzNmUyZjY0OTQzMGRmMzIzOSJ9'
                            . '.IrFJgYefZi6xodW0-KaGHF9ICJPQoyfmESx_dpgRdSg'
    ;


    public function testCreate()
    {
        $createdToken = Jwt::create('http://localhost:8000', 'GET', 'issuer', 'secret');

        $data = Jwt::decodeWithoutVerifying($createdToken);
        $expectedData = Jwt::decodeWithoutVerifying($this->token);

        $this->assertEquals($expectedData->header, $data->header, 'Wrong header.');
        $this->assertEquals($expectedData->body->iss, $data->body->iss, 'Wrong issuer.');
        $this->assertEquals($expectedData->body->qsh, $data->body->qsh, 'Wrong qsh.');
    }

    public function testDecodeWithoutVerifying()
    {
        $this->assertEquals((object) [
            'header' => (object) [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ],
            'body' => (object) [
                'iss' => 'issuer',
                'iat' => 1593435338,
                'exp' => 1593521738,
                'qsh' => 'c88caad15a1c1a900b8ac08aa9686f4e8184539bea1deda36e2f649430df3239',
            ],
            'signature' => 'IrFJgYefZi6xodW0-KaGHF9ICJPQoyfmESx_dpgRdSg',
        ], Jwt::decodeWithoutVerifying($this->token));
    }

    public function testVerify()
    {
        $this->assertTrue(Jwt::verify($this->token, 'secret', 'http://localhost:8000', 'GET'));
    }

    public function testVerifyFailIfWrongSecret()
    {
        $this->assertFalse(Jwt::verify($this->token, 'wrong secret', 'http://localhost:8000', 'GET'));
    }

    public function testVerifyFailIfWrongQsh()
    {
        $this->assertFalse(Jwt::verify($this->token, 'secret', 'http://localhost:8000', 'POST'));
    }
}
