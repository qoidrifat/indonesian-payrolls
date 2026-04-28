<?php

namespace App\DTO;

final readonly class BpjsResultDTO
{
    public function __construct(
        public float $baseWage,
        public float $jhtEmployee,
        public float $jhtEmployer,
        public float $jpEmployee,
        public float $jpEmployer,
        public float $jkkEmployer,
        public float $jkmEmployer,
        public float $kesehatanEmployee,
        public float $kesehatanEmployer,
        public array $ratesSnapshot,
    ) {
    }

    public function totalEmployee(): float
    {
        return round($this->jhtEmployee + $this->jpEmployee + $this->kesehatanEmployee, 2);
    }

    public function totalEmployer(): float
    {
        return round(
            $this->jhtEmployer + $this->jpEmployer + $this->jkkEmployer
            + $this->jkmEmployer + $this->kesehatanEmployer,
            2
        );
    }

    /**
     * Sum of employer contributions that count as taxable benefit-in-kind
     * for the employee (Kesehatan + JKK + JKM under UU HPP 2021).
     */
    public function taxableEmployerBenefits(): float
    {
        return round($this->kesehatanEmployer + $this->jkkEmployer + $this->jkmEmployer, 2);
    }

    public function toArray(): array
    {
        return [
            'base_wage'           => $this->baseWage,
            'jht_employee'        => $this->jhtEmployee,
            'jht_employer'        => $this->jhtEmployer,
            'jp_employee'         => $this->jpEmployee,
            'jp_employer'         => $this->jpEmployer,
            'jkk_employer'        => $this->jkkEmployer,
            'jkm_employer'        => $this->jkmEmployer,
            'kesehatan_employee'  => $this->kesehatanEmployee,
            'kesehatan_employer'  => $this->kesehatanEmployer,
            'total_employee'      => $this->totalEmployee(),
            'total_employer'      => $this->totalEmployer(),
            'rates_snapshot'      => $this->ratesSnapshot,
        ];
    }
}
