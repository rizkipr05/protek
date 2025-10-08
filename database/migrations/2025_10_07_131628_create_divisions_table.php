<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();                       // unsigned BIGINT auto-increment
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('divisions');
    }
};
