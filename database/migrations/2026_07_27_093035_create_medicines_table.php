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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->string('category');

            $table->string('batch_number')
                ->unique();

            $table->date('expiry_date');

            $table->decimal('buying_price',10,2);

            $table->decimal('selling_price',10,2);

            $table->integer('quantity')
                ->default(0);

            $table->string('supplier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
