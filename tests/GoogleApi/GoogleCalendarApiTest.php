<?php

namespace App\Tests\GoogleApi;

use App\GoogleApi\GoogleCalendarApi;
use Google_Client;

class GoogleCalendarApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function インスタンスを作成できる()
    {
        $client = $this->createMock(Google_Client::class);
        $obj = new GoogleCalendarApi($client);

        $this->assertInstanceOf(GoogleCalendarApi::class, $obj);
        return $obj;
    }
}
