<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','division_id')) {
                $table->foreignId('division_id')->nullable()->after('role')
                      ->constrained('divisions')->nullOnDelete();
            }
            if (!Schema::hasColumn('users','is_active')) {
                $table->boolean('is_active')->default(true)->after('division_id');
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('role'); // <-- HANYA kolom
            }
            if (!Schema::hasColumn('users','is_active')) {
                $table->boolean('is_active')->default(true)->after('division_id');
            }
        });
    }
};
