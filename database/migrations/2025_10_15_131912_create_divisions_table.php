<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            // Pakai ULID biar rapi dan aman diekspos ke client
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Opsional, enak kalau ingin “hapus” tanpa hilang data
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
