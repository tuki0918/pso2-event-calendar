<?php

namespace App\Entity\ValueObject;

class CampaignId
{
    /** @var string */
    private $value;

    /**
     * CampaignId constructor.
     * @param null|string $id
     */
    private function __construct(?string $id)
    {
        $this->value = $id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value();
    }

    /**
     * @param null|string $id
     * @return CampaignId
     */
    public static function create(?string $id): self
    {
        return new self($id);
    }

    /**
     * @return null|string
     */
    public function value(): ?string
    {
        return $this->value;
    }
}
