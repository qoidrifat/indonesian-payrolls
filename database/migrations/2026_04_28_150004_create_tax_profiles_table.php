<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tax_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('has_npwp')->default(false);
            $table->string('npwp_number', 32)->nullable();
            $table->enum('ptkp_status', ['TK/0','TK/1','TK/2','TK/3','K/0','K/1','K/2','K/3'])->default('TK/0');
            $table->char('ter_category', 1)->nullable(); // A | B | C
            $table->boolean('is_foreign_tax_resident')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_profiles');
    }
};
