<?php

namespace App\Tests\GoogleApi;

use App\GoogleApi\GoogleApi;
use Google_Client;

class GoogleApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function インスタンスを作成できる()
    {
        $client = $this->createMock(Google_Client::class);
        $obj = new GoogleApi($client);

        $this->assertInstanceOf(GoogleApi::class, $obj);
    }
}
