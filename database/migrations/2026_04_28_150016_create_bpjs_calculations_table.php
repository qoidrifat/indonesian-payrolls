<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpjs_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('base_wage', 15, 2);

            $table->decimal('jht_employee', 15, 2)->default(0);
            $table->decimal('jht_employer', 15, 2)->default(0);
            $table->decimal('jp_employee', 15, 2)->default(0);
            $table->decimal('jp_employer', 15, 2)->default(0);
            $table->decimal('jkk_employer', 15, 2)->default(0);
            $table->decimal('jkm_employer', 15, 2)->default(0);
            $table->decimal('kesehatan_employee', 15, 2)->default(0);
            $table->decimal('kesehatan_employer', 15, 2)->default(0);

            $table->decimal('total_employee', 15, 2)->default(0);
            $table->decimal('total_employer', 15, 2)->default(0);

            $table->json('rates_snapshot')->nullable(); // for audit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_calculations');
    }
};
