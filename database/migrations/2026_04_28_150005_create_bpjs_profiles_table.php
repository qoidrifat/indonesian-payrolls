<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bpjs_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('ketenagakerjaan_number', 32)->nullable();
            $table->string('kesehatan_number', 32)->nullable();

            $table->boolean('enroll_jht')->default(true);
            $table->boolean('enroll_jp')->default(true);
            $table->boolean('enroll_jkk')->default(true);
            $table->boolean('enroll_jkm')->default(true);
            $table->boolean('enroll_kesehatan')->default(true);

            $table->enum('jkk_risk_grade', ['I','II','III','IV','V'])->default('I');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_profiles');
    }
};
