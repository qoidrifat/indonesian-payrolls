<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_code', 32)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 32)->nullable();
            $table->string('nik', 32)->nullable()->comment('Nomor Induk Kependudukan');
            $table->string('npwp', 32)->nullable()->comment('Nomor Pokok Wajib Pajak');
            $table->string('bpjs_kesehatan', 32)->nullable();
            $table->string('bpjs_ketenagakerjaan', 32)->nullable();
            $table->string('bank_name', 64)->nullable();
            $table->string('bank_account_number', 64)->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('position');
            $table->string('department', 64)->nullable();
            $table->enum('employment_status', ['permanent', 'contract', 'probation', 'intern'])->default('permanent');
            $table->enum('marital_status', ['TK', 'K0', 'K1', 'K2', 'K3'])->default('TK')->comment('PTKP status');
            $table->date('join_date');
            $table->date('end_date')->nullable();
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
