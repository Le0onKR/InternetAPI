<?php

declare(strict_types=1);

namespace AidenKR\InternetAPI;

readonly class InternetOptions
{
    public function __construct(
        protected int $timeout = 50
    ) {}

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}