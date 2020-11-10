<?php

namespace AtlassianConnectLaravel\Tests\Unit\Auth;

use AtlassianConnectLaravel\Auth\Qsh;
use PHPUnit\Framework\TestCase;

class QshTest extends TestCase
{
    /**
     * @dataProvider canonicalMethodProvider
     */
    public function testCanonicalMethod($method, $canonicalMethod)
    {
        $qsh = new Qsh('', $method);

        $this->assertEquals($canonicalMethod, $qsh->canonicalMethod());
    }

    public function canonicalMethodProvider()
    {
        return [
            ['get', 'GET'],
            ['Get', 'GET'],
            ['GET', 'GET'],
            ['post', 'POST'],
            ['Post', 'POST'],
            ['POST', 'POST'],
        ];
    }

    /**
     * @dataProvider canonicalUriProvider
     */
    public function testCanonicalUri($uri, $canonicalUri)
    {
        $qsh = new Qsh($uri, '');

        $this->assertEquals($canonicalUri, $qsh->canonicalUri());
    }

    public function canonicalUriProvider()
    {
        return [
            // Base url is not supported https://developer.atlassian.com/cloud/bitbucket/query-string-hash/#canonical-uri
            ['https://addon.example.com', '/'],
            ['https://addon.example.com/issue', '/issue'],
            ['https://addon.example.com/title&description', '/title%26description'],
            ['https://example.atlassian.net/rest/api/2/issue/', '/rest/api/2/issue'],
        ];
    }

    /**
     * @dataProvider canonicalQueryProvider
     */
    public function testCanonicalQuery($type, $queryString, $canonicalQueryString)
    {
        $qsh = new Qsh('?' . $queryString, 'GET');

        $this->assertEquals($canonicalQueryString, $qsh->canonicalQuery(), $type);
    }

    public function canonicalQueryProvider()
    {
        return [
            ['Ignore the JWT parameter', 'jwt=ABC.DEF.GHI', ''],
            ['Ignore the JWT parameter', 'expand=names&jwt=ABC.DEF.GHI', 'expand=names'],

            ['URL-encode parameter keys', 'enabled', 'enabled'],
            ['URL-encode parameter keys', 'some+spaces+in+this+parameter', 'some%20spaces%20in%20this%20parameter'],
            ['URL-encode parameter keys', 'connect*', 'connect%2A'],
            ['URL-encode parameter keys', '1+%2B+1+equals+3', '1%20%2B%201%20equals%203'],
            ['URL-encode parameter keys', 'in+%7E3+days', 'in%20~3%20days'],

            //URL-encode parameter values

            ['URL-encode parameter values', 'param=value', 'param=value'],
            ['URL-encode parameter values', 'param=some+spaces+in+this+parameter', 'param=some%20spaces%20in%20this%20parameter'],
            ['URL-encode parameter values', 'query=connect*', 'query=connect%2A'],
            ['URL-encode parameter values', 'a=b&', 'a=b'],
            ['URL-encode parameter values', 'director=%E5%AE%AE%E5%B4%8E%20%E9%A7%BF', 'director=%E5%AE%AE%E5%B4%8E%20%E9%A7%BF'],

            ['URL-encoding is upper case', 'director=%e5%ae%ae%e5%b4%8e%20%e9%a7%bf', 'director=%E5%AE%AE%E5%B4%8E%20%E9%A7%BF'],

            ['Sort query parameter keys', 'a=x&b=y', 'a=x&b=y'],
            ['Sort query parameter keys', 'a10=1&a1=2&b1=3&b10=4', 'a1=2&a10=1&b1=3&b10=4'],
            // In docs example here is a mistake and query string is =A&a=a&b=b&B=B
            ['Sort query parameter keys', 'A=A&a=a&b=b&B=B', 'A=A&B=B&a=a&b=b'],

            ['Sort query parameter value lists', 'ids=-1&ids=1&ids=10&ids=2&ids=20', 'ids=-1,1,10,2,20'],
            ['Sort query parameter value lists', 'ids=.1&ids=.2&ids=%3A1&ids=%3A2', 'ids=.1,.2,%3A1,%3A2'],
            ['Sort query parameter value lists', 'ids=10%2C2%2C20%2C1', 'ids=10%2C2%2C20%2C1'],
            ['Sort query parameter value lists', 'tuples=1%2C2%2C3&tuples=6%2C5%2C4&tuples=7%2C9%2C8', 'tuples=1%2C2%2C3,6%2C5%2C4,7%2C9%2C8'],
            ['Sort query parameter value lists', 'chars=%E5%AE%AE&chars=%E5%B4%8E&chars=%E9%A7%BF', 'chars=%E5%AE%AE,%E5%B4%8E,%E9%A7%BF'],
            ['Sort query parameter value lists', 'c=&c=+&c=%2520&c=%2B', 'c=,%20,%2520,%2B'],
            ['Sort query parameter value lists', 'a=x1&a=x10&b=y1&b=y10', 'a=x1,x10&b=y1,y10'],
            ['Sort query parameter value lists', 'a=another+one&a=one+string&b=and+yet+more&b=more+here', 'a=another%20one,one%20string&b=and%20yet%20more,more%20here'],
            ['Sort query parameter value lists', 'a=1%2C2%2C3&a=4%2C5%2C6&b=a%2Cb%2Cc&b=d%2Ce%2Cf', 'a=1%2C2%2C3,4%2C5%2C6&b=a%2Cb%2Cc,d%2Ce%2Cf'],
        ];
    }

    /**
     * @dataProvider canonicalRequestProvider
     */
    public function testCanonicalRequest($url, $method, $claim)
    {
        $qsh = new Qsh($url, $method);

        $this->assertEquals($claim, $qsh->create());
    }

    public function canonicalRequestProvider()
    {
        return [
            // This test doesn't work, I tested it in typescript package atlassian-jwt and there is the same result as at me
            // ['/test¶m=value', 'GET',  'be16910858a41fd19ea5c1b4e9decca9a784d1024cb00b2158defe2f29dc86dd'],
            ['/rest/api/2/issue', 'POST', '43dd1779e33c34fae00c308d62e5dd153a32147d1bcb5d40b3936457fda0ece4'],
        ];
    }
}
