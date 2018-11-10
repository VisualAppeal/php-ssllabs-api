<?php

use PHPUnit\Framework\TestCase;

use VisualAppeal\SslLabs;

class SslLabsTest extends TestCase
{
    /**
     * @var SslLabs
     */
    protected $sslLabs;

    protected function setUp()
    {
        $this->sslLabs = new SslLabs;
    }

    public function testInfo(): void
    {
        $info = $this->sslLabs->info();
        $this->assertObjectHasAttribute('engineVersion', $info);
    }

    public function testStringResponse(): void
    {
        $this->sslLabs = new SslLabs(false);
        $info = $this->sslLabs->info();
        $this->assertInternalType('string', $info);
    }

    public function testAnalyze(): void
    {
        $host = 'https://www.ssllabs.com';

        $analyze = $this->sslLabs->analyze($host);
        $this->assertObjectHasAttribute('host', $analyze);
        $this->assertEquals($host, $analyze->host);
    }

    public function testAnalyzeCached(): void
    {
        $host = 'https://www.ssllabs.com';

        $analyze = $this->sslLabs->analyzeCached($host);
        $this->assertObjectHasAttribute('host', $analyze);
        $this->assertEquals($host, $analyze->host);
    }

    public function testGetEndpointData(): void
    {
        $host = 'https://www.ssllabs.com';
        $ip = '64.41.200.100';

        $endpointData = $this->sslLabs->getEndpointData($host, $ip);
        $this->assertInternalType('object', $endpointData);
    }

    public function testGetStatusCodes(): void
    {
        $statusCodes = $this->sslLabs->getStatusCodes();
        $this->assertInternalType('object', $statusCodes);
    }

    public function testGetRootCertsRaw(): void
    {
        $rootCertsRaw = $this->sslLabs->getRootCertsRaw();
        $this->assertNull($rootCertsRaw);
    }
}
