<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tax_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->unique()->constrained()->cascadeOnDelete();

            $table->enum('method', ['ter', 'progressive']);
            $table->char('ter_category', 1)->nullable();
            $table->string('ptkp_status', 8);
            $table->decimal('ptkp_amount', 15, 2);

            $table->decimal('gross_taxable', 15, 2);
            $table->decimal('biaya_jabatan', 15, 2)->default(0);
            $table->decimal('jht_jp_employee', 15, 2)->default(0);
            $table->decimal('net_taxable', 15, 2)->default(0);
            $table->decimal('pkp', 15, 2)->default(0);

            $table->decimal('ter_rate', 8, 6)->nullable();
            $table->decimal('pph21_amount', 15, 2);

            // Year-to-date used for December reconciliation:
            $table->decimal('ytd_gross', 18, 2)->default(0);
            $table->decimal('ytd_pph21', 18, 2)->default(0);

            $table->boolean('non_npwp_surcharge_applied')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_calculations');
    }
};
