<?php

namespace App\Tests\Entity\ValueObject;

use App\Entity\ValueObject\CampaignId;

class CampaignIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function IDを作成できる()
    {
        $obj = CampaignId::create($id = 999);
        $this->assertInstanceOf(CampaignId::class, $obj);
        $this->assertEquals($obj->value(), $id);
        $this->assertEquals((string)$obj, $id);
    }

    /**
     * @test
     * @expectedException \ArgumentCountError
     */
    public function 未指定時にはIDを作成できない()
    {
        $obj = CampaignId::create();
    }

    /**
     * @test
     * @dataProvider dInvalid
     * @expectedException \TypeError
     * @param $id
     */
    public function IDを作成できない($id)
    {
        $obj = CampaignId::create($id);
    }

    /**
     * 無効なデータ
     * @return array
     */
    public function dInvalid(): array
    {
        return [
            'array' => [[999]],
            'object' => [(object)['id' => 999]],
            'class' => [new \DateTime()],
        ];
    }
}