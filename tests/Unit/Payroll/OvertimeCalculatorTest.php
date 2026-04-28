<?php

use App\Services\Payroll\OvertimeCalculator;

beforeEach(function () {
    $this->calc = new OvertimeCalculator();
});

it('calculates 3 hours of workday overtime per PP 35/2021', function () {
    $monthlyWage = 5_000_000;
    $result = $this->calc->calculate($monthlyWage, hours: 3, dayType: 'workday');

    $hourly = $monthlyWage / 173;
    $expected = round(($hourly * 1.5) + ($hourly * 2 * 2), 2);

    expect($result->amount)->toBe($expected)
        ->and($result->breakdown)->toHaveCount(3);
});

it('calculates 8 hours of rest day overtime under 5-day workweek', function () {
    $monthlyWage = 5_000_000;
    $result = $this->calc->calculate($monthlyWage, hours: 8, dayType: 'rest_day', workweek: 'five_days');

    $hourly = $monthlyWage / 173;
    // 5-day rest day: hours 1-8 all at 2× (per-hour rounding to match calculator)
    $expected = round($hourly * 2, 2) * 8;

    expect($result->amount)->toBe($expected);
});

it('uses 6-day workweek schedule for rest day', function () {
    $monthlyWage = 5_000_000;
    $result = $this->calc->calculate($monthlyWage, hours: 9, dayType: 'rest_day', workweek: 'six_days');

    $hourly = $monthlyWage / 173;
    // 6-day rest day: hours 1-7 at 2×, hour 8 at 3×, hour 9 at 4× (per-hour rounding)
    $expected = round($hourly * 2, 2) * 7 + round($hourly * 3, 2) + round($hourly * 4, 2);

    expect($result->amount)->toBe(round($expected, 2));
});
