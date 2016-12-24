<?php

namespace App\Tests\Entity\ValueObject;

use App\Entity\ValueObject\CampaignPeriod;
use DateInterval;
use DateTimeImmutable;

class CampaignPeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dValid
     * @param $startAt
     * @param $end
     */
    public function 対象期間を作成できる($startAt, $endAt)
    {
        $obj = CampaignPeriod::create($startAt, $endAt);
        $this->assertInstanceOf(CampaignPeriod::class, $obj);
        $this->assertInstanceOf(DateTimeImmutable::class, $obj->start());

        if (!is_null($obj->end())) {
            $this->assertInstanceOf(DateTimeImmutable::class, $obj->end());
        }

        $this->assertEquals($obj->start(), $startAt);
        $this->assertEquals($obj->end(), $endAt);
    }

    /**
     * @test
     * @dataProvider dInvalid
     * @expectedException \TypeError
     * @param $startAt
     * @param $end
     */
    public function 対象期間を作成できない($startAt, $endAt)
    {
        $obj = CampaignPeriod::create($startAt, $endAt);
    }

    /**
     * 正常なデータ
     * @return array
     */
    public function dValid(): array
    {
        $startAt = new DateTimeImmutable();
        $endAt = $startAt->add(new DateInterval('PT30M'));
        return [
            'startAt and endAt' => [$startAt, $endAt],
            'startAt and null' => [$startAt, null],
        ];
    }

    /**
     * 無効なデータ
     * @return array
     */
    public function dInvalid(): array
    {
        $startAt = new DateTimeImmutable();
        $endAt = $startAt->add(new DateInterval('PT30M'));
        return [
            'null and endAt' => [null, $endAt],
            'null and null' => [null, null],
        ];
    }
}