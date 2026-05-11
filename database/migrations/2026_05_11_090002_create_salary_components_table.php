<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['allowance', 'deduction', 'bonus']);
            $table->string('name');
            $table->decimal('amount', 14, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_recurring')->default(true)->comment('Auto-include each payroll run');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
