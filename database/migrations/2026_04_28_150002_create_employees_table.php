<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('employee_number')->unique();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->string('religion')->nullable();
            $table->string('citizenship')->default('WNI');

            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();

            $table->date('hire_date');
            $table->date('end_date')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'probation', 'intern'])->default('permanent');
            $table->enum('status', ['active', 'inactive', 'terminated', 'resigned'])->default('active');
            $table->enum('workweek', ['five_days', 'six_days'])->default('five_days');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
