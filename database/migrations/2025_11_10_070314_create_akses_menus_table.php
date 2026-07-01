<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akses_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('asal_cabang')->nullable();
            $table->foreignId('cabang_id')->nullable()->constrained('master_cabangs')->nullOnDelete();
            $table->enum('akses_cabang', ['spesifik', 'semua'])->default('spesifik');
            $table->longText('akses_menu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses_menus');
    }
};
