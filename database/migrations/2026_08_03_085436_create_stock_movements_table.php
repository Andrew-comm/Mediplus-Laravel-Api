<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('medicine_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'opening',
                'purchase',
                'sale',
                'adjustment',
                'expired',
                'returned'
            ]);

            $table->integer('quantity');

            $table->string('reference')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
