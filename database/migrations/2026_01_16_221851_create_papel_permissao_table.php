<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('papel_permissao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('papel_id');
            $table->unsignedBigInteger('permissao_id');

            $table->foreign('papel_id')->references('id')->on('papeis')->onDelete('cascade');
            $table->foreign('permissao_id')->references('id')->on('permissoes')->onDelete('cascade');

            $table->unique(['papel_id', 'permissao_id'], 'uk_papel_permissao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('papel_permissao');
    }
};