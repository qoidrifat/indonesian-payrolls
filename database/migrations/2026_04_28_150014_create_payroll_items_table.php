<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('fixed_allowance', 15, 2)->default(0);
            $table->decimal('variable_allowance', 15, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('thr_amount', 15, 2)->default(0);
            $table->decimal('bonus_amount', 15, 2)->default(0);
            $table->decimal('other_earnings', 15, 2)->default(0);

            $table->decimal('gross_salary', 15, 2)->default(0);

            $table->decimal('bpjs_employee_total', 15, 2)->default(0);
            $table->decimal('bpjs_employer_total', 15, 2)->default(0);
            $table->decimal('pph21_amount', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);

            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);

            $table->json('breakdown')->nullable(); // detailed component snapshot
            $table->string('idempotency_key', 64)->unique();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
