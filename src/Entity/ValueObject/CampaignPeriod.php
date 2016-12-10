<?php

namespace App\Entity\ValueObject;

use DateTimeImmutable;

class CampaignPeriod
{
    /** @var DateTimeImmutable */
    private $start;
    /** @var DateTimeImmutable|null */
    private $end;

    /**
     * CampaignPeriod constructor.
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable|null $end
     */
    private function __construct(
        DateTimeImmutable $start,
        ?DateTimeImmutable $end
    ) {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable|null $end
     * @return CampaignPeriod
     */
    public static function create(
        DateTimeImmutable $start,
        ?DateTimeImmutable $end
    ): self {
        return new self($start, $end);
    }

    /**
     * 開始時刻
     * @return DateTimeImmutable
     */
    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * 終了時刻
     * @return DateTimeImmutable|null
     */
    public function end(): ?DateTimeImmutable
    {
        return $this->end;
    }
}
