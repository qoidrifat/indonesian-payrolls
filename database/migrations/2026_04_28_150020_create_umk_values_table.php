<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('umk_values', function (Blueprint $table) {
            $table->id();
            $table->string('province');
            $table->string('city')->nullable();
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->unique(['province', 'city', 'year'], 'umk_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umk_values');
    }
};
