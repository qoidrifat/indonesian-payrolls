<?php

namespace App\DTO;

final readonly class TaxResultDTO
{
    public function __construct(
        public string $method,             // 'ter' | 'progressive'
        public ?string $terCategory,
        public string $ptkpStatus,
        public float $ptkpAmount,
        public float $grossTaxable,
        public float $biayaJabatan,
        public float $jhtJpEmployee,
        public float $netTaxable,
        public float $pkp,
        public ?float $terRate,
        public float $pph21Amount,
        public bool $nonNpwpSurchargeApplied,
        public float $ytdGross = 0.0,
        public float $ytdPph21 = 0.0,
    ) {
    }

    public function toArray(): array
    {
        return [
            'method'                       => $this->method,
            'ter_category'                 => $this->terCategory,
            'ptkp_status'                  => $this->ptkpStatus,
            'ptkp_amount'                  => $this->ptkpAmount,
            'gross_taxable'                => $this->grossTaxable,
            'biaya_jabatan'                => $this->biayaJabatan,
            'jht_jp_employee'              => $this->jhtJpEmployee,
            'net_taxable'                  => $this->netTaxable,
            'pkp'                          => $this->pkp,
            'ter_rate'                     => $this->terRate,
            'pph21_amount'                 => $this->pph21Amount,
            'non_npwp_surcharge_applied'   => $this->nonNpwpSurchargeApplied,
            'ytd_gross'                    => $this->ytdGross,
            'ytd_pph21'                    => $this->ytdPph21,
        ];
    }
}
