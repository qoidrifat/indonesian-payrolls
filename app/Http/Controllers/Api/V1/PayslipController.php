<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayslipController extends Controller
{
    public function show(Payslip $payslip): JsonResponse
    {
        return response()->json($payslip->load(['employee', 'period']));
    }

    public function download(Payslip $payslip): StreamedResponse
    {
        abort_unless($payslip->pdf_path && Storage::disk($payslip->pdf_disk)->exists($payslip->pdf_path), 404);

        return Storage::disk($payslip->pdf_disk)->download(
            $payslip->pdf_path,
            $payslip->payslip_number . '.pdf',
        );
    }
}
