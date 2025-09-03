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
        Schema::create('songs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->integer('views_count')->default(0);
            $table->string('status');

            $table->timestamps();

            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();

            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_by')->nullable();

            $table->softDeletes();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
