<?php

namespace App\Parser\Response;

use App\Entity\Campaign;
use App\Parser\Core\ParseResponse;

class CampaignResponse extends ParseResponse
{
    /**
     * @param string $title
     * @param Campaign[] ...$campaigns
     * @return CampaignResponse
     */
    public static function create(
        string $title,
        Campaign ...$campaigns
    ) {
        return new self($title, $campaigns);
    }
}
