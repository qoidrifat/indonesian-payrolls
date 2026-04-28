<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->string('payslip_number')->unique();
            $table->string('pdf_path')->nullable();
            $table->string('pdf_disk')->default('local');
            $table->json('snapshot');
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'payroll_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
