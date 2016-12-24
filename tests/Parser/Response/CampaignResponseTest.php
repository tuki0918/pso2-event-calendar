<?php

namespace App\Tests\Parser\Response;

use App\Entity\Campaign;
use App\Parser\Response\CampaignResponse;

class CampaignResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dValid
     */
    public function レスポンスを作成できる($campaigns)
    {
        $obj = CampaignResponse::create($title = 'title', ...$campaigns);

        $this->assertInstanceOf(CampaignResponse::class, $obj);
        $this->assertEquals($obj->title(), $title);
        $this->assertInternalType('array', $obj->data());
    }

    /**
     * 正常なデータ
     * @return array
     */
    public function dValid(): array
    {
        $campaign = $this->createMock(Campaign::class);

        return [
            'empty arg' => [[]],
            'one arg' => [[$campaign]],
            'two args' => [[$campaign, $campaign]],
            'three args' => [[$campaign, $campaign, $campaign]],
        ];
    }
}
