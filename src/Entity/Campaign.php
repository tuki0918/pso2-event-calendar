<?php

namespace App\Entity;

use App\Entity\Core\Entity;
use App\Entity\Enum\CampaignType;
use App\Entity\ValueObject\CampaignId;
use App\Entity\ValueObject\CampaignPeriod;
use DateTimeImmutable;

class Campaign extends Entity
{
    /** @var CampaignId */
    private $id;
    /** @var CampaignPeriod */
    private $period;
    /** @var CampaignType */
    private $type;
    /** @var string */
    private $description;

    /**
     * Event constructor.
     * @param CampaignId $id
     * @param CampaignPeriod $period
     * @param CampaignType $type
     * @param string $description
     */
    private function __construct(
        CampaignId $id,
        CampaignPeriod $period,
        CampaignType $type,
        string $description
    ) {
        $this->id = $id;
        $this->period = $period;
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * @param CampaignId $id イベント識別子
     * @param CampaignPeriod $period
     * @param CampaignType $type
     * @param string $description
     * @return Campaign
     */
    public static function create(
        CampaignId $id,
        CampaignPeriod $period,
        CampaignType $type,
        string $description
    ): self {
        return new self($id, $period, $type, $description);
    }

    /**
     * イベント期間
     * @return DateTimeImmutable
     */
    public function period(): CampaignPeriod
    {
        return $this->period;
    }

    /**
     * イベントタイプ
     * @return CampaignType
     */
    public function type(): CampaignType
    {
        return $this->type;
    }

    /**
     * イベント内容
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }
}
