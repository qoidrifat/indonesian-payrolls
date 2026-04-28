<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_structure_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            // 'earning' adds to gross; 'deduction' subtracts after tax/BPJS computed
            $table->enum('type', ['earning', 'deduction']);
            // 'fixed' = tunjangan tetap (counts toward BPJS base); 'variable' = tidak tetap
            $table->enum('category', ['basic', 'fixed_allowance', 'variable_allowance', 'benefit', 'deduction'])->default('fixed_allowance');
            $table->decimal('amount', 15, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('counts_toward_bpjs')->default(false);
            $table->boolean('prorate_on_attendance')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['salary_structure_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
