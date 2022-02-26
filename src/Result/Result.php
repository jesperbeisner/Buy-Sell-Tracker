<?php

declare(strict_types=1);

namespace App\Result;

abstract class Result
{
    public const FAILURE = 0;
    public const SUCCESS = 1;

    /**
     * @param mixed[] $data
     */
    public function __construct(
        private int $result,
        private string $message,
        private array $data = [],
    ) {}

    public function getResult(): int
    {
        return $this->result;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }
}
