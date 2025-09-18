<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('job_order_id')->unique()->constrained()->onDelete('cascade');
            $table->string('truck_no');
            $table->string('NoPalka')->nullable();
            $table->string('NoHose')->nullable();
            $table->time('plugging')->nullable();
            $table->time('open_valve')->nullable();
            $table->time('close_valve')->nullable();
            $table->time('unplugging')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_lists');
    }
};
