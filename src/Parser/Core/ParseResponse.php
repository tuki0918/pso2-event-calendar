<?php

namespace App\Parser\Core;

abstract class ParseResponse
{
    /** @var string */
    private $title;
    /** @var array */
    private $data;

    /**
     * ParseResponse constructor.
     * @param string $title
     * @param array $data
     */
    protected function __construct(
        string $title,
        array $data
    ) {
        $this->title = $title;
        $this->data = $data;
    }

    /**
     * タイトル
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * データ
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}
