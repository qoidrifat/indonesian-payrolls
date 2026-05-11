<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->decimal('base_salary', 14, 2)->default(0);
            $table->decimal('allowances', 14, 2)->default(0);
            $table->decimal('overtime_pay', 14, 2)->default(0);
            $table->decimal('bonus', 14, 2)->default(0);
            $table->decimal('gross_salary', 14, 2)->default(0);

            $table->decimal('bpjs_kesehatan_employee', 14, 2)->default(0);
            $table->decimal('bpjs_kesehatan_company', 14, 2)->default(0);
            $table->decimal('bpjs_jht_employee', 14, 2)->default(0);
            $table->decimal('bpjs_jht_company', 14, 2)->default(0);
            $table->decimal('bpjs_jp_employee', 14, 2)->default(0);
            $table->decimal('bpjs_jp_company', 14, 2)->default(0);
            $table->decimal('pph21', 14, 2)->default(0);
            $table->decimal('other_deductions', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);

            $table->decimal('net_salary', 14, 2)->default(0);

            $table->unsignedSmallInteger('working_days')->default(0);
            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('overtime_minutes')->default(0);

            $table->json('breakdown')->nullable()->comment('Computed snapshot of components');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->unique(['payroll_id', 'employee_id']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
