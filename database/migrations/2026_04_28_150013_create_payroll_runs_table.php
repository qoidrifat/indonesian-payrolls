<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('run_type', ['monthly', 'thr', 'bonus', 'final_settlement'])->default('monthly');
            $table->enum('status', ['draft', 'calculating', 'calculated', 'approved', 'paid', 'failed'])->default('draft');

            $table->unsignedInteger('employee_count')->default(0);
            $table->decimal('total_gross', 18, 2)->default(0);
            $table->decimal('total_net', 18, 2)->default(0);
            $table->decimal('total_tax', 18, 2)->default(0);
            $table->decimal('total_bpjs_employee', 18, 2)->default(0);
            $table->decimal('total_bpjs_employer', 18, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('calculated_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};
