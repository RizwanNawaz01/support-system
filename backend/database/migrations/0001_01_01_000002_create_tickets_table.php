<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
        {
            Schema::create('tickets', function (Blueprint $table) {
                $table->ulid('id')->primary();
                $table->string('subject');
                $table->text('body');
                $table->string('status', 32)->default('open')->index();
                $table->string('category', 64)->nullable()->index();
                $table->text('note')->nullable();

                $table->text('ai_explanation')->nullable();
                $table->decimal('ai_confidence', 5, 2)->nullable();

                $table->boolean('manually_overridden')->default(false)->index();

                $table->timestamps();
                $table->index(['created_at']);
            });
        }


    public function down(): void
        {
            Schema::dropIfExists('tickets');
        }
};
