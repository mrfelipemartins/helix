<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helix_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('index')->index();
            $table->enum('type', [
                'create',
                'drop',
                'insert',
                'delete',
                'search',
                'maintenance',
            ])->index();
            $table->enum('level', ['info', 'warn', 'error'])->default('info')->index();
            $table->string('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helix_activities');
    }
};
