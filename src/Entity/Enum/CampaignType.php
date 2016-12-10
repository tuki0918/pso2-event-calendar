<?php

namespace App\Entity\Enum;

use App\Entity\Core\Enum;

class CampaignType extends Enum
{
    const BOOST = 'boost';
    const EMERGENCY = 'emergency';
    const NETCAFE = 'netcafe';
    const CASINO = 'casino';
    const CHALLENGE = 'challenge';
    const UNKNOWN = 'unknown';

    /**
     * ブーストイベント
     * @return CampaignType
     */
    public static function Boost(): self
    {
        return new self(self::BOOST);
    }

    /**
     * 予告緊急クエスト
     * @return CampaignType
     */
    public static function Emergency(): self
    {
        return new self(self::EMERGENCY);
    }

    /**
     * 公認ネットカフェ
     * @return CampaignType
     */
    public static function NetCafe(): self
    {
        return new self(self::NETCAFE);
    }

    /**
     * カジノイベント
     * @return CampaignType
     */
    public static function Casino(): self
    {
        return new self(self::CASINO);
    }

    /**
     * チャレンジマイルアップイベント
     * @return CampaignType
     */
    public static function Challenge(): self
    {
        return new self(self::CHALLENGE);
    }

    /**
     * 未実装
     * @return CampaignType
     */
    public static function Unknown(): self
    {
        return new self(self::UNKNOWN);
    }
}
