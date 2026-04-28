<?php

use App\Services\Tax\ProgressiveTaxTable;

it('applies layered Article 17 brackets', function () {
    $table = new ProgressiveTaxTable();

    expect($table->calculate(0))->toBe(0.0);
    expect($table->calculate(60_000_000))->toBe(3_000_000.0);              // 5% × 60M
    expect($table->calculate(100_000_000))->toBe(3_000_000.0 + 6_000_000); // 5% × 60M + 15% × 40M = 9M
    expect($table->calculate(300_000_000))->toBe(3_000_000.0 + 28_500_000.0 + 12_500_000.0); // 5% × 60M + 15% × 190M + 25% × 50M
});
