<?php

namespace App\DTO;

final readonly class OvertimeResultDTO
{
    public function __construct(
        public float $hours,
        public float $hourlyRate,
        public float $amount,
        /** @var array<int, array{hour:int, multiplier:float, amount:float}> */
        public array $breakdown,
    ) {
    }

    public function toArray(): array
    {
        return [
            'hours'       => $this->hours,
            'hourly_rate' => $this->hourlyRate,
            'amount'      => $this->amount,
            'breakdown'   => $this->breakdown,
        ];
    }
}
